<?php

namespace App\Jobs;

use App\Models\Actor;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

    public function __construct(protected ExtractionRun $run) {}

    public function handle(): void
    {
        try {
            $this->run->bucket?->touch();
            $this->run->saved_extractor?->touch();

            $this->run->status = RunStatus::Running;
            $this->run->started_at = now();
            $this->run->save();

            $data = Magic::extract()
                ->model(ElElEm::fromString($this->run->model))
                ->instructions($this->run->saved_extractor->output_instructions)
                ->schema($this->run->target_schema)
                ->strategy($this->run->strategy)
                ->chunkSize($this->run->chunk_size)
                ->contextOptions($this->run->getContextOptions())
                ->artifacts($this->run->bucket->artifacts->all())
                ->onMessage(function (Message $message, ?string $actorId = null) {
                    /** @var ?Actor $actor */
                    $actor = $this->run->actors()->find($actorId);

                    if (! $actor) {
                        Log::warning('Actor not found', [
                            'runId' => $this->run->id,
                            'actorId' => $actorId,
                            'message' => $message->toArray()
                        ]);
                    }

                    $actor?->add($message);
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
                    $this->run->partial_data = $data;
                    $this->run->save();
                })
                ->stream();

            $this->run->data = $data;
            $this->run->status = RunStatus::Completed;
            $this->run->finished_at = now();
            $this->run->save();
        } catch (\Throwable|ErrorException $e) {
            $this->run->error = [
                'title' => method_exists($e, 'getTitle') ? $e->getTitle() : null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];
            $this->run->status = RunStatus::Failed;
            $this->run->finished_at = now();
            $this->run->save();

            if (app()->isProduction()) {
                report($e);
            } else {
                Log::error($e->getMessage(), ['exception' => $e]);
            }
        }

        $this->run->dispatchWebhook();
    }
}
