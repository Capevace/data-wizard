<?php

namespace App\Jobs;

use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use App\Models\File;
use App\Models\User;
use Capevace\MagicImport\Artifacts\ArtifactGenerationStatus;
use Capevace\MagicImport\Config\Extractor;
use Capevace\MagicImport\Functions\ExtractData;
use Capevace\MagicImport\LLM\ElElEm;
use Capevace\MagicImport\LLM\Models\Claude3Family;
use Capevace\MagicImport\Loop\InferenceResult;
use Capevace\MagicImport\Prompt\TokenStats;
use Capevace\MagicImport\Strategies\SimpleStrategy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Swaggest\JsonSchema\Schema;

class GenerateDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ExtractionRun $run, protected ?User $startedBy)
    {
    }

    public function handle(): void
    {
        /** @var ?File $file */
        $file = $this->run->bucket->files->first();

        if (!$file?->artifact || $file->artifact_status !== ArtifactGenerationStatus::Complete) {
            return;
        }

        $extractor = new Extractor(
            id: 'estate',
            title: 'Estate',
            description: null,
            allowedTypes: [
                'images',
                'documents'
            ],
            llm: ElElEm::fromString(ElElEm::id('anthropic', Claude3Family::SONNET_3_5)),
            schema: $this->run->target_schema_typed,
            strategy: 'simple',
        );

        $artifacts = [$file->artifact];

        try {
            $this->run->status = RunStatus::Running;
            $this->run->save();

            $strategy = new SimpleStrategy(
                extractor: $extractor,
                onDataProgress: function (InferenceResult $result) {
                    $this->run->partial_result_json = $result->value;
                    $this->run->save();
                },
                onTokenStats: function (TokenStats $tokenStats) {
                    dump($tokenStats);
                    $this->run->token_stats = $tokenStats;
                    $this->run->save();
                }
            );

            $result = $strategy->run(artifacts: $artifacts);

            $this->run->result_json = $result->value;
            $this->run->status = RunStatus::Completed;
            $this->run->save();
        } catch (\Exception $e) {
            report($e);

            $this->run->error = [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
            $this->run->status = RunStatus::Failed;
            $this->run->save();
        }
    }
}
