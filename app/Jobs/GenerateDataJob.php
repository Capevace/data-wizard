<?php

namespace App\Jobs;

use App\Models\Actor;
use App\Models\Actor\ActorTelemetry;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use App\Models\File;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Mateffy\Magic\Artifacts\ArtifactGenerationStatus;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\LLM\Models\Gpt4Family;
use Mateffy\Magic\LLM\Models\OpenRouter;
use Mateffy\Magic\Magic;
use Mateffy\Magic\Prompt\TokenStats;

class GenerateDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected ExtractionRun $run, protected ?User $startedBy = null) {}

    public function handle(): void
    {
        /** @var Collection<File> $files */
        $artifacts = $this->run->bucket->files
            ->filter(fn (File $file) => $file->artifact && $file->artifact_status === ArtifactGenerationStatus::Complete)
            ->sortBy(fn (File $file) => $file->artifact->getMetadata()->name)
            ->values()
            ->pluck('artifact');

        try {
            $this->run->bucket?->logUsage();
            $this->run->saved_extractor?->logUsage();

            $this->run->status = RunStatus::Running;
            $this->run->save();

            $data = Magic::extract()
//                ->model(OpenRouter::model(OpenRouter::X_AI_GROK_BETA))
                ->model(Gpt4Family::model('gpt-4o-mini'))
                ->system($this->run->saved_extractor->output_instructions)
                ->schema($this->run->target_schema)
                ->strategy($this->run->strategy)
                ->artifacts($artifacts->all())
                ->onMessage(function (Message $message, ?string $actorId = null) {
                    /** @var ?Actor $actor */
                    $actor = $this->run->actors()->find($actorId);

                    if (! $actor) {
                        throw new \InvalidArgumentException("Actor {$actorId} not found");
                    }

                    $actor->add($message);
                })
                ->onTokenStats(function (TokenStats $tokenStats) {
                    $this->run->token_stats = $tokenStats;
                    $this->run->save();
                })
                ->onActorTelemetry(function (ActorTelemetry $telemetry) {
                    /** @var ?Actor $actor */
                    $actor = $this->run->actors()->find($telemetry->id);

                    if (! $actor) {
                        $actor = $this->run->actors()->make();
                        $actor->id = $telemetry->id;
                    }

                    $actor->fill($telemetry->toDatabase());
                    $actor->save();
                })
                ->onDataProgress(function (array $data) {
                    $this->run->partial_result_json = $data;
                    $this->run->save();
                })
                ->stream();

            $this->run->result_json = $data->toArray() ?? $this->run->result_json;
            $this->run->status = RunStatus::Completed;
            $this->run->save();
        } catch (\Throwable $e) {
            $this->run->error = [
                'title' => method_exists($e, 'getTitle') ? $e->getTitle() : null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
            $this->run->status = RunStatus::Failed;
            $this->run->save();

            report($e);
        }
    }
}
