<?php

namespace App\Filament\Resources\ExtractionBucketResource\Widgets\Concerns;

use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;
use Mateffy\Color;
use Mateffy\Magic\Extraction\ContextOptions;
use Mateffy\Magic\Extraction\EvaluationType;

/**
 * @property-read ExtractionBucket $bucket
 * @property-read Collection<ExtractionRun> $runs
 */
trait HasBucketForEvaluation
{
    #[Locked]
    public string $bucketId;

    #[Reactive]
    public ?string $model = null;

    #[Reactive]
    public bool $colorByCount = false;

    #[Computed]
    public function bucket(): ExtractionBucket
    {
        return ExtractionBucket::findOrFail($this->bucketId);
    }

    #[Computed]
    public function runs(): Collection
    {
        return $this->bucket->runs()
            ->when($this->model, fn ($query, $model) => $query->where('model', $model))
//            ->where('strategy', '!=', 'simple')
            ->where('status', RunStatus::Completed)
            ->get();
    }

    protected function colorByEvaluation(EvaluationType $type, string $shade = '600'): string
    {
        return match ($type) {
            default => Color\Shades::tailwind('amber')
                ->{"shade{$shade}"}
                ->opacity(0.7)
                ->toRgbaString(),
            EvaluationType::TextOnly => Color\Shades::tailwind('blue')
                ->{"shade{$shade}"}
                ->opacity(0.65)
                ->toRgbaString(),
            EvaluationType::EmbeddedImages => Color\Shades::tailwind('purple')
                ->{"shade{$shade}"}
                ->opacity(0.65)
                ->toRgbaString(),
            EvaluationType::PageImages => Color\Shades::tailwind('pink')
                ->{"shade{$shade}"}
                ->opacity(0.65)
                ->toRgbaString(),
        };
    }

    protected function colorByCount(int $count, string $shade = '600'): string
    {
        return match (true) {
            $count <= 2 => Color\Shades::tailwind('red')
                ->{"shade{$shade}"}
                ->opacity(0.5)
                ->toRgbaString(),
            $count <= 5 => Color\Shades::tailwind('yellow')
                ->{"shade{$shade}"}
                ->opacity(0.5)
                ->toRgbaString(),
            default => Color\Shades::tailwind('green')
                ->{"shade{$shade}"}
                ->opacity(0.5)
                ->toRgbaString(),
        };
    }

    /**
     * @param Closure(ExtractionRun):int|float $getDataUsing
     * @return array<Collection<string>, Collection<array{label: string, context: string, data: array<int|float|null>}>>
     */
    public function groupDataByStrategy(Closure $getDataUsing, string $method = 'average'): array
    {
        $makeKey = function (ExtractionRun $run) {
            $strategy = Str::title($run->strategy);
            $type = $run->getContextOptions()->getEvaluationTypeLabel();

            return "{$strategy} ({$type})";
        };

        $strategies = $this->runs->pluck('strategy')->unique();

        $runs_grouped_by_media_context = $this->runs
            ->groupBy(fn (ExtractionRun $run) => $run->getContextOptions()->getEvaluationType())
            ->sortBy(fn (Collection $runs, string $context) => EvaluationType::from($context)->getOrder());

        $datasets = $runs_grouped_by_media_context
            ->map(function (Collection $runs_with_same_context, string $context) use ($method, $strategies, $getDataUsing) {
                $context_label = EvaluationType::from($context)->getLabel();

                $countsByStrategy = $strategies
                    ->mapWithKeys(fn (string $strategy) => [
                        $strategy => $runs_with_same_context
                            ->filter(fn (ExtractionRun $run) => $run->strategy === $strategy)
                            ->count()
                    ]);

                $valueByStrategy = $strategies
                    ->mapWithKeys(fn (string $strategy) => [
                        $strategy => $runs_with_same_context
                            ->filter(fn (ExtractionRun $run) => $run->strategy === $strategy)
                            ->map($getDataUsing)
                            ->{$method}()
                    ]);

                return [
                    'label' => "With {$context_label}",
                    'context' => $context,
                    'strategies' => $strategies->values(),
                    'counts' => $countsByStrategy->values(),
                    'borderColor' => $countsByStrategy
                        ->map(fn (int $count, string $strategy) => $this->shouldUseCountColoring()
                            ? $this->colorByCount(count: $count, shade: '950')
                            : $this->colorByEvaluation(EvaluationType::from($context), shade: '600')
                        )
                        ->values()
                        ->toArray(),
                    'backgroundColor' => $countsByStrategy
                        ->map(fn (int $count, string $strategy) => $this->shouldUseCountColoring()
                            ? $this->colorByCount(count: $count, shade: '600')
                            : $this->colorByEvaluation(EvaluationType::from($context), shade: '600')
                        )
                        ->values()
                        ->toArray(),
                    'data' => $valueByStrategy->values()
                ];
            });

        return [$strategies, $datasets];
    }

    public function shouldUseCountColoring(): bool
    {
        return $this->colorByCount;
    }
}
