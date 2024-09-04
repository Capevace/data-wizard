<?php

namespace App\Jobs;

use App\Models\Actor;
use App\Models\Actor\ActorTelemetry;
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
use Capevace\MagicImport\LLM\Models\GroqLlama3;
use Capevace\MagicImport\LLM\Options\ElElEmOptions;
use Capevace\MagicImport\Loop\InferenceResult;
use Capevace\MagicImport\Loop\Response\JsonResponse;
use Capevace\MagicImport\Loop\Response\Streamed\PartialJsonResponse;
use Capevace\MagicImport\Model\Exceptions\InvalidRequest;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\TokenStats;
use Capevace\MagicImport\Strategies\ParallelStrategy;
use Capevace\MagicImport\Strategies\SequentialStrategy;
use Capevace\MagicImport\Strategies\SimpleStrategy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Swaggest\JsonSchema\Schema;

class GenerateDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ExtractionRun $run, protected ?User $startedBy)
    {
    }

    public function handle(): void
    {
        /** @var Collection<File> $files */
        $artifacts = $this->run->bucket->files
            ->filter(fn(File $file) => $file->artifact && $file->artifact_status === ArtifactGenerationStatus::Complete)
            ->sortBy(fn(File $file) => $file->artifact->getMetadata()->name)
            ->values()
            ->pluck('artifact');

        $extractor = new Extractor(
            id: $this->run->saved_extractor->id,
            title: $this->run->saved_extractor->label ?? $this->run->saved_extractor->id,
            outputInstructions: $this->run->saved_extractor->output_instructions,
            allowedTypes: [
                'images',
                'documents'
            ],
            llm: ElElEm::fromString(ElElEm::id('anthropic', Claude3Family::SONNET_3_5)),
//            llm: new GroqLlama3(
//                model: 'llama-3.1-70b-versatile',
//                options: new ElElEmOptions(
//                    maxTokens: 2048
//                )
//            ),
            schema: $this->run->target_schema,
            strategy: $this->run->strategy,
        );

        try {
            $this->run->bucket?->logUsage();
            $this->run->saved_extractor?->logUsage();

            $this->run->status = RunStatus::Running;
            $this->run->save();

            $strategyClass = match ($this->run->strategy) {
                'simple' => SimpleStrategy::class,
                'sequential' => SequentialStrategy::class,
                'parallel' => ParallelStrategy::class,
                default => throw new \InvalidArgumentException("Unknown strategy type: {$this->run->strategy}"),
            };

            $strategy = new $strategyClass(
                extractor: $extractor,
                onActorTelemetry: function (ActorTelemetry $telemetry) {
                    /** @var ?Actor $actor */
                    $actor = $this->run->actors()->find($telemetry->id);

                    if (!$actor) {
                        $actor = $this->run->actors()->make();
                        $actor->id = $telemetry->id;
                    }

                    $actor->fill($telemetry->toDatabase());
                    $actor->save();
                },
                onDataProgress: function (array $data) {
                    $this->run->partial_result_json = $data;
                    $this->run->save();
                },
                onMessage: function (Message $message, string $actorId) {
                    /** @var ?Actor $actor */
                    $actor = $this->run->actors()->find($actorId);

                    if (!$actor) {
                        throw new \InvalidArgumentException("Actor {$actorId} not found");
                    }

                    $actor->add($message);
                },
                onTokenStats: function (TokenStats $tokenStats) {
                    $this->run->token_stats = $tokenStats;
                    $this->run->save();
                }
            );

            $result = $strategy->run(artifacts: $artifacts->all());

            $this->run->result_json = $result->value ?? $this->run->result_json;
            $this->run->status = RunStatus::Completed;
            $this->run->save();
        } catch (\Exception $e) {
            report($e);

            $this->run->error = [
                'title' => method_exists($e, 'getTitle') ? $e->getTitle() : null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
            $this->run->status = RunStatus::Failed;
            $this->run->save();
        }
    }
}
