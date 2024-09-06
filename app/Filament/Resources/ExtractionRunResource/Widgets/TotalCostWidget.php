<?php

namespace App\Filament\Resources\ExtractionRunResource\Widgets;

use Akaunting\Money\Money;
use App\Models\ExtractionRun;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalCostWidget extends BaseWidget
{
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $json_extract = fn (string $path) => "json_extract(token_stats, '\$.{$path}')";
        $data = ExtractionRun::query()
            ->selectRaw(collect([
                "sum({$json_extract('input_tokens')}) as input_tokens",
                "sum({$json_extract('output_tokens')}) as output_tokens",
                "sum({$json_extract('cost.input_cents_per_1k')} * {$json_extract('input_tokens')}) as input_total",
                "sum({$json_extract('cost.output_cents_per_1k')} * {$json_extract('output_tokens')}) as output_total",
                'sum(json_extract(token_stats, "$.cost.input_cents_per_1k")) as sum_input_euros_per_1k',
                'sum(json_extract(token_stats, "$.cost.output_cents_per_1k")) as sum_output_euros_per_1k',
                'count(*) as count',
            ])->join(', '))
            ->first();

        $countWhereHasInputCentsPer1k = ExtractionRun::query()
            ->selectRaw(collect([
                'json_extract(token_stats, \'$.cost.input_cents_per_1k\') as input_cents_per_1k',
                'count(*) as count',
            ])->join(', '))
            ->where('input_cents_per_1k', '>', 0)
            ->first();

        $countWhereHasOutputCentsPer1k = ExtractionRun::query()
            ->selectRaw(collect([
                'json_extract(token_stats, \'$.cost.output_cents_per_1k\') as output_cents_per_1k',
                'count(*) as count',
            ])->join(', '))
            ->where('output_cents_per_1k', '>', 0)
            ->first();

        //        dd($data, $countWhereHasInputCentsPer1k);

        /** @var Money $totalCost */
        $totalCost = ExtractionRun::query()
            ->get()
            ->reduce(function (Money $totalCost, ExtractionRun $run) {
                return $totalCost->add($run->calculateTotalCost());
            }, Money::EUR(0));

        return [
            Stat::make(
                'Total Input Tokens',
                $data->input_tokens
            ),
            Stat::make(
                'Total Output Tokens',
                $data->output_tokens
            ),
            Stat::make(
                'Total Tokens',
                $data->input_tokens + $data->output_tokens
            ),
            Stat::make(
                'Average Cost per 1M Input-Tokens',
                Money::EUR($data->sum_input_euros_per_1k * 1000 / $countWhereHasInputCentsPer1k->count)
            ),
            Stat::make(
                'Average Cost per 1M Output-Tokens',
                Money::EUR($data->sum_output_euros_per_1k * 1000 / $countWhereHasOutputCentsPer1k->count)
            ),
            Stat::make(
                'Total Cost',
                $totalCost->format()
            ),
        ];
    }
}
