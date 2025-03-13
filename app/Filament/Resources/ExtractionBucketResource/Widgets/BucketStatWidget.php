<?php

namespace App\Filament\Resources\ExtractionBucketResource\Widgets;

use App\Filament\Resources\ExtractionBucketResource\Widgets\Concerns\HasBucketForEvaluation;
use App\Models\ExtractionRun;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Mateffy\Magic\Extraction\ContextOptions;

class BucketStatWidget extends StatsOverviewWidget
{
    use HasBucketForEvaluation;

    protected function getColumns(): int
    {
        return 4;
    }

    //    protected static ?string $heading = 'Average duration in seconds by strategy and context';

    protected function getData(): array
    {
        [$strategies, $datasets] = $this->groupDataByStrategy(fn (ExtractionRun $run) => $run->duration->totalSeconds, method: 'average');

        return [
            'labels' => $strategies
                ->map(fn (string $strategy) => Str::title($strategy))
                ->values()
                ->toArray(),
            'datasets' => $datasets->values()->toArray()
        ];
    }

    protected function getStats(): array
    {
        if ($this->runs->isEmpty()) {
            return [];
        }

        $format = fn ($value) => Number::format($value, precision: 0, locale: 'de_DE');

        $run_tokens = $this->runs->map(fn (ExtractionRun $run) => $run->getEnrichedTokenStats()->tokens);

        /** @var ?ExtractionRun $slowest_run */
        /** @var ?ExtractionRun $fastest_run */
        /** @var ?ExtractionRun $run_with_highest_tokens */
        /** @var ?ExtractionRun $run_with_lowest_tokens */
        /** @var ?ExtractionRun $fastest_run_without_simple_strategy */
        /** @var ?ExtractionRun $run_with_lowest_tokens_without_simple_strategy */

        $run_with_lowest_tokens = null;
        $run_with_highest_tokens = null;
        $slowest_run = null;
        $fastest_run = null;
        $fastest_run_without_simple_strategy = null;
        $run_with_lowest_tokens_without_simple_strategy = null;
        $slowest_run_without_double_pass_strategy = null;
        $run_with_highest_tokens_without_double_pass_strategy = null;

        foreach ($this->runs as $run) {
            if ($run_with_lowest_tokens === null || $run->getEnrichedTokenStats()->tokens < $run_with_lowest_tokens->getEnrichedTokenStats()->tokens) {
                $run_with_lowest_tokens = $run;
            }
            if ($run_with_highest_tokens === null || $run->getEnrichedTokenStats()->tokens > $run_with_highest_tokens->getEnrichedTokenStats()->tokens) {
                $run_with_highest_tokens = $run;
            }
            if ($slowest_run === null || $run->duration->totalSeconds > $slowest_run->duration->totalSeconds) {
                $slowest_run = $run;
            }
            if ($fastest_run === null || $run->duration->totalSeconds < $fastest_run->duration->totalSeconds) {
                $fastest_run = $run;
            }

            if ($run->strategy !== 'simple' && ($fastest_run_without_simple_strategy === null || $run->duration->totalSeconds < $fastest_run_without_simple_strategy->duration->totalSeconds)) {
                $fastest_run_without_simple_strategy = $run;
            }

            if ($run->strategy !== 'double_pass' && ($slowest_run_without_double_pass_strategy === null || $run->duration->totalSeconds > $slowest_run_without_double_pass_strategy->duration->totalSeconds)) {
                $slowest_run_without_double_pass_strategy = $run;
            }

            if ($run->strategy !== 'double_pass' && ($run_with_highest_tokens_without_double_pass_strategy === null || $run->getEnrichedTokenStats()->tokens > $run_with_highest_tokens_without_double_pass_strategy->getEnrichedTokenStats()->tokens)) {
                $run_with_highest_tokens_without_double_pass_strategy = $run;
            }

            if ($run->strategy !== 'simple' && ($run_with_lowest_tokens_without_simple_strategy === null || $run->getEnrichedTokenStats()->tokens < $run_with_lowest_tokens_without_simple_strategy->getEnrichedTokenStats()->tokens)) {
                $run_with_lowest_tokens_without_simple_strategy = $run;
            }
        }

        $average_duration = $this->runs->average(fn (ExtractionRun $run) => $run->duration->totalSeconds);
        $average_tokens = $run_tokens->average();
        $median_tokens = $run_tokens->median();

        return [
            Stat::make('Dataset tokens', $format($this->bucket->calculateInputTokens(ContextOptions::all()))),
            Stat::make('Average run tokens', $format($average_tokens)),
            Stat::make('Median run tokens', $format($median_tokens)),
            Stat::make('Average duration', $format($average_duration) . 's'),

            $slowest_run !== null
                ? Stat::make('Slowest run', "{$slowest_run->duration->totalSeconds} seconds")
                    ->icon('bi-stopwatch')
                    ->description("Strategy: {$slowest_run->strategy} – Context: {$slowest_run->getContextOptions()->getEvaluationTypeLabel()}")
                : null,

            $fastest_run !== null
                ? Stat::make('Fastest run', "{$fastest_run->duration->totalSeconds} seconds")
                    ->icon('bi-stopwatch')
                    ->description("Strategy: {$fastest_run->strategy} – Context: {$fastest_run->getContextOptions()->getEvaluationTypeLabel()}")
                : null,

            $run_with_highest_tokens !== null
                ? Stat::make('Most tokens', $format($run_with_highest_tokens->getEnrichedTokenStats()->tokens))
                    ->icon('bi-braces-asterisk')
                    ->description("Strategy: {$run_with_highest_tokens->strategy} – Context: {$run_with_highest_tokens->getContextOptions()->getEvaluationTypeLabel()}")
                : null,

            $run_with_lowest_tokens !== null
                ? Stat::make('Fewest tokens', $format($run_with_lowest_tokens->getEnrichedTokenStats()->tokens))
                    ->icon('bi-braces-asterisk')
                    ->description("Strategy: {$run_with_lowest_tokens->strategy} – Context: {$run_with_lowest_tokens->getContextOptions()->getEvaluationTypeLabel()}")
                : null,

        ];
    }
}
