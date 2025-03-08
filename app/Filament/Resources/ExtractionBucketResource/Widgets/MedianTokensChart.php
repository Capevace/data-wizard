<?php

namespace App\Filament\Resources\ExtractionBucketResource\Widgets;

use App\Filament\Resources\ExtractionBucketResource\Widgets\Concerns\HasBucketForEvaluation;
use App\Models\ExtractionRun;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class MedianTokensChart extends ChartWidget
{
    use HasBucketForEvaluation;

    protected static ?string $heading = 'Median tokens by strategy and context';

	protected function getFilters(): ?array
	{
		return [
			'total' => 'Total Tokens',
			'input' => 'Input Tokens',
			'output' => 'Output Tokens',
		];
	}

	protected function getData(): array
    {
		$filter = $this->filter ?? 'total';

        [$strategies, $datasets] = $this->groupDataByStrategy(fn (ExtractionRun $run) => match ($filter) {
			'total' => $run->getEnrichedTokenStats()->tokens,
			'input' => $run->getEnrichedTokenStats()->inputTokens,
			'output' => $run->getEnrichedTokenStats()->outputTokens,
		}, method: 'median');

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

                                return `\${tooltipItem.dataset.label}: \${tooltipItem.raw} tokens / \${count} \${runLabel}`;
                            }
                        }
                    }
                },
                onClick: function(event, elements) {
                    if (elements.length) {
                        const element = elements[0];
                        const datasetIndex = element.datasetIndex;
                        const index = element.index;
                        const strategy = this.data.datasets[datasetIndex].strategies[index];
                        const context = this.data.datasets[datasetIndex].context;
                        // Add your custom logic here

                        window.Livewire.dispatch('setRunFilter', { strategy: strategy, context: context });
                    }
                }
            }
        JS);
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
