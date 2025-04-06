<?php

namespace App\Jobs;

use App\Models\ExtractionRun;
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
            $this->run->execute();
        } catch (\Throwable $e) {
            if (app()->isProduction()) {
                report($e);
            } else {
                Log::error($e->getMessage(), ['exception' => $e]);
            }
        }

    }
}
