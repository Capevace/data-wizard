<?php

namespace App\Console\Commands;

use App\Models\ExtractionRun;
use Illuminate\Console\Command;
use Mateffy\Magic\LLM\Models\Claude3Family;

class ResetTokenCostsCommand extends Command
{
    protected $signature = 'reset:token-costs';

    protected $description = 'Command description';

    public function handle(): void
    {
        $runs = ExtractionRun::query()
            ->get();

        foreach ($runs as $run) {
            /** @var \App\Models\ExtractionRun $run */

            $run->token_stats = $run->token_stats?->withCost(Claude3Family::sonnet_3_5()->getModelCost());
            $run->save();
        }
    }
}
