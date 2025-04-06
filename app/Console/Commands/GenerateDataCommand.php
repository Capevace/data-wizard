<?php

namespace App\Console\Commands;

use App\Jobs\GenerateArtifactJob;
use App\Jobs\GenerateDataJob;
use App\Models\ArtifactGenerationStatus;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use App\Models\File;
use App\Models\SavedExtractor;
use Illuminate\Console\Command;
use Illuminate\Queue\Queue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mateffy\Magic;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;

/**
 * Usage like: php artisan wizard --schema=products.json exposé.pdf other-file.docx
 */
class GenerateDataCommand extends Command
{
	protected $signature = 'wizard {--model= : The model to generate data with} {--strategy=simple : The strategy to generate data with}  {--extractor= : The extractor to use} {--no-cache : Do not use the cache for the files} {--output= : The path to output the file as} {files* : The files to generate data from}';

	protected $description = 'Command description';

	public function handle(): void
	{
        $model = $this->option('model') ?? Magic::defaultModelName();
        $extractor = $this->option('extractor');
        $strategy = $this->option('strategy');

        if ($extractor === null) {
            $this->error('You must provide an extractor.');
            return;
        }

        $files = $this->argument('files');

        if (empty($files)) {
            $this->error('You must provide at least one file to generate data from.');
            return;
        }

        $files = collect($files)
            ->map(fn ($file) => realpath($file))
            ->filter(fn ($file) => $file !== false)
            ->values()
            ->toArray();

        /**
         * @var null|class-string<Magic\Extraction\Strategies\Strategy> $strategyClass
         */
        $strategyClass = Magic::getExtractionStrategies()->get($strategy);

        if ($strategyClass === null) {
            $this->error("Unknown strategy: {$strategy}");
            return;
        }

        $strategyLabel = $strategyClass::getLabel();

        $extractorModel = SavedExtractor::find($extractor);

        if ($extractorModel === null) {
            $this->error("Unknown extractor: {$extractor}");
            return;
        }

        try {
            $llm = Magic\Models\ElElEm::fromString($model);
        } catch (\InvalidArgumentException $e) {
            $this->error("Unknown model: {$model}");
            return;
        }

        $this->line("Extractor: {$extractorModel->label}");
        $this->line("Model: {$llm->getModelLabel()}");
        $this->line("Strategy: {$strategyLabel}");

        $bucket = null;

        spin(
            callback: function () use (&$bucket, $model, $files) {
                $bucket = $this->setupBucket($model, $files);
                $this->line("Bucket ID: {$bucket->id}");

                if ($bucket->files->isEmpty()) {
                    $this->error("No files to process.");
                    return;
                }

                $complete = false;
                while ($complete === false) {
                    sleep(1);

                    $bucket->refresh();

                    $errors = $bucket
                        ->files
                        ->filter(fn (File $file) => $file->artifact_status === ArtifactGenerationStatus::Failed)
                        ->values();

                    if ($errors->isNotEmpty()) {
                        $this->error("Failed files:");
                        $errors->each(fn (File $file) => $this->error("{$file->path}"));

                        throw new \RuntimeException('Failed to generate artifacts.');
                    }

                    $complete = $bucket
                        ->files
                        ->every(fn (File $file) => $file->artifact_status === ArtifactGenerationStatus::Complete);
                }
            },
            message: 'Generating artifacts...',
        );

        $run = $bucket->runs()->make([
            'strategy' => $strategy,
            'target_schema' => $extractorModel->json_schema,
            'saved_extractor_id' => $extractorModel->id,
            'model' => $model,
            'include_text' => $extractorModel->include_text,
            'include_embedded_images' => $extractorModel->include_embedded_images,
            'mark_embedded_images' => $extractorModel->mark_embedded_images,
            'include_page_images' => $extractorModel->include_page_images,
            'mark_page_images' => $extractorModel->mark_page_images,
            'chunk_size' => $extractorModel->chunk_size,
        ]);

        $run->id = Str::uuid()->toString();
        $run->saveQuietly();

        $this->line("Run ID: {$run->id}");

        spin(
            callback: function () use ($run) {
                GenerateDataJob::dispatchSync($run);
            },
            message: 'Extracting data...',
        );

        if ($run->status === RunStatus::Failed) {
            $this->error("Extraction failed: " . json_encode($run->error));
            return;
        }

        $this->line("Extraction complete.");

        $cwd = getcwd();
        $timestamp = now()->format('Y-m-d-H-i-s');
        $filename = $this->option('output');
        $filename ??= "extraction-{$run->id}-{$timestamp}.json";

        $path = "{$cwd}/{$filename}";

        $json = json_encode($run->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        file_put_contents($path, $json);

        $tokenStats = $run->getEnrichedTokenStats();

        $this->line("Duration: {$run->formatted_duration}");
        $this->line("Input Tokens: {$tokenStats->inputTokens}");
        $this->line("Output Tokens: {$tokenStats->outputTokens}");

        if ($cost = $tokenStats->calculateTotalCost()) {
            $this->line("Cost: {$cost}");
        }

        $this->info("Data saved to: {$path}");
	}

    protected function setupBucket(string $model, array $files): ExtractionBucket
    {
        $key = collect($files)
            ->map(fn ($file) => md5_file($file))
            ->unique()
            ->sort()
            ->implode('-');

        $key = "bucket-cache-" . md5($model . $key);

        if (!$this->option('no-cache') && cache()->has($key)) {
            $cachedBucketId = cache()->get($key);

            $bucket = ExtractionBucket::find($cachedBucketId);

            if ($bucket !== null) {
                $this->warn("Using cached bucket.");
                return $bucket;
            } else {
                cache()->forget($key);
            }
        }

        // Backup the current queue connection
        $originalQueueConnection = Config::get('queue.default');

        try {
            // Set the queue connection to 'sync' for this job
            Config::set('queue.default', 'sync');

            $bucket = DB::transaction(function () use ($model, $files) {
                $bucket = ExtractionBucket::create();

                foreach ($files as $path) {
                    $file = $bucket->addMedia($path)->preservingOriginal()->toMediaCollection('files');


                    $bucket->files()->save($file);
                }

                $bucket->save();

                return $bucket;
            });

            cache()->put($key, $bucket->id);
        } finally {
            // Restore the original queue connection
            Config::set('queue.default', $originalQueueConnection);
        }

        return $bucket;
    }

    protected function start()
    {

    }
}
