<?php

namespace App\Filament\Resources\ExtractionBucketResource\Widgets;

use App\Filament\Resources\ExtractionBucketResource\Widgets\Concerns\HasBucketForEvaluation;
use App\Models\ExtractionRun;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class AverageDurationChart extends ChartWidget
{
    use HasBucketForEvaluation;

    protected static ?string $heading = 'Average duration in seconds by strategy and context';

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

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<JS
            {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const count = tooltipItem.dataset.counts[tooltipItem.dataIndex];
                                const runLabel = count === 1 ? 'run' : 'runs';

                                return `\${tooltipItem.dataset.label}: \${tooltipItem.raw.toFixed(2)} seconds / \${count} \${runLabel}`;
                            }
                        },
                    }
                },
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
