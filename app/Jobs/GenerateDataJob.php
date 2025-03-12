<?php

namespace App\Jobs;

use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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

            $data = $this->run->makeMagicExtractor()->stream();

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
