<?php

namespace App\Jobs;

use App\Models\Actor;
use App\Models\ArtifactGenerationStatus;
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
use Illuminate\Support\Facades\Log;
use Mateffy\Magic;
use Mateffy\Magic\Chat\ActorTelemetry;
use Mateffy\Magic\Chat\Messages\Message;
use Mateffy\Magic\Chat\TokenStats;
use Mateffy\Magic\Models\ElElEm;
use OpenAI\Exceptions\ErrorException;

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
                ->model(ElElEm::fromString($this->run->model))
                ->system($this->run->saved_extractor->output_instructions)
                ->schema($this->run->target_schema)
                ->strategy($this->run->strategy)
                ->artifacts($artifacts->all())
                ->onMessage(function (Message $message, ?string $actorId = null) {
                    /** @var ?Actor $actor */
                    $actor = $this->run->actors()->find($actorId);

                    if (! $actor) {
                        $actor = $this->run->actors()->make();
                        $actor->id = $actorId;
                        $actor->save();
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
        } catch (\Throwable|ErrorException $e) {
            $this->run->error = [
                'title' => method_exists($e, 'getTitle') ? $e->getTitle() : null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
            $this->run->status = RunStatus::Failed;
            $this->run->save();

            if (app()->isProduction()) {
                report($e);
            } else {
                Log::error($e->getMessage(), ['exception' => $e]);
            }
        }
    }
}
