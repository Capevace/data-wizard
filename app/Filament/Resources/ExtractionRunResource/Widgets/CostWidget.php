<?php

namespace App\Filament\Resources\ExtractionRunResource\Widgets;

use App\Models\ExtractionRun;
use Carbon\CarbonImmutable;
use Filament\Widgets\ChartWidget as BaseWidget;
use Illuminate\Support\Collection;

class CostWidget extends BaseWidget
{
    protected static bool $isLazy = true;

    protected function getData(): array
    {
        $numDays = 1;
        $from = now()->subDays($numDays)->startOfDay();
        $to = now()->endOfDay();

        $hours = collect(range(0, $from->diffInHours($to)))
            ->map(fn (int $hours) => now()->toImmutable()->subHours($hours)->startOfHour())
            ->reverse()
            ->values();

        $runs = ExtractionRun::query()
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->get()
            ->groupBy(fn (ExtractionRun $run) => $run->created_at->startOfHour()->format('Y-m-d H:i'))
            ->map(fn (Collection $runs) => $runs->sum(fn (ExtractionRun $run) => $run->token_stats?->tokens));

        return [
            'datasets' => [
                [
                    'label' => 'Tokens per hour',
                    'data' => $hours
                        ->map(fn (CarbonImmutable $hour) => $runs->get($hour->format('Y-m-d H:i')) ?? 0)
                        ->all(),
                ],
            ],

            'labels' => $hours->map(fn (CarbonImmutable $hour) => $hour->format('Y-m-d H:i')),
        ];

        //        return [
        //             'datasets' => [
        //                [
        //                    'label' => 'Blog posts created',
        //                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
        //                ],
        //            ],
        //            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        //        ];
    }

    //    protected function getStats(): array
    //    {
    //        return [
    //            Stat::make(
    //                'Total Money Spent',
    //                ExtractionRun::query()
    //                    // token_stats->tokens
    //                    ->selectRaw('sum(json_extract(token_stats, \'$.tokens\')) as total, sum(json_extract(token_stats, \'$.input_tokens\')) as input, sum(json_extract(token_stats, \'$.output_tokens\')) as output')
    //                    ->first()
    //                    ->total
    //            )
    //        ];
    //    }
    protected function getType(): string
    {
        return 'line';
    }
}
