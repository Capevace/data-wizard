<?php

namespace App\Filament\Resources\ExtractionBucketResource\Pages;

use App\Filament\Forms\ModelSelect;
use App\Filament\Resources\ExtractionBucketResource;
use App\Filament\Resources\ExtractionBucketResource\Actions\StartEvaluationRuns;
use App\Filament\Resources\ExtractionBucketResource\RelationManagers\RunsRelationManager;
use App\Filament\Resources\ExtractionBucketResource\Widgets\AverageDurationChart;
use App\Filament\Resources\ExtractionBucketResource\Widgets\AverageTokensChart;
use App\Filament\Resources\ExtractionBucketResource\Widgets\CorrectnessChart;
use App\Filament\Resources\ExtractionBucketResource\Widgets\MedianDurationChart;
use App\Filament\Resources\ExtractionBucketResource\Widgets\MedianTokensChart;
use App\Models\ExtractionBucket;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Form;
use Filament\Resources\Pages\Concerns\HasRelationManagers;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Session;
use Mateffy\Magic\Models\ElElEm;

/**
 * @property-read ExtractionBucket $record
 */
class EvaluateExtractionBucket extends ViewRecord
{
    use HasRelationManagers;
    use InteractsWithRecord;

    protected static string $resource = ExtractionBucketResource::class;
    protected static string $view = 'filament.pages.evaluate-extraction-bucket';

    public ?string $model = null;

    #[Session]
    public bool $colorByCount = false;

    #[Computed]
    public function model_tabs(): Collection
    {
        $tabs = $this->record->runs
            ->pluck('model')
            ->unique()
            ->values()
            ->mapWithKeys(fn (string $model) => [
                $model => ElElEm::fromString($model)->getModelLabel()
            ]);

        return $tabs;
    }

    public function updatedModel(): void
    {
        $this->dispatch('setModel', model: $this->model);
    }

    public function getTitle(): string|Htmlable
    {
        return __('Evaluate Bucket Runs');
    }

    public function getBreadcrumb(): string
    {
        return __('Evaluate');
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('setToColorByCount')
                    ->label(__('Color by run count'))
                    ->icon('bi-123')
                    ->color(fn () => $this->colorByCount ? 'primary' : 'gray')
                    ->action(fn () => $this->colorByCount = true),
                Action::make('setToColorByEvaluation')
                    ->label(__('Color by evaluation type'))
                    ->icon('bi-gear')
                    ->color(fn () => $this->colorByCount ? 'gray' : 'primary')
                    ->action(fn () => $this->colorByCount = false),
            ])
                ->size('lg')
                ->color('gray')
                ->dropdownPlacement('bottom-end')
                ->iconButton()
                ->label(__('Coloring'))
                ->tooltip(__('Coloring'))
                ->icon('heroicon-o-swatch'),

            StartEvaluationRuns::make()
                ->bucket(fn () => $this->record),
        ];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getStatisticsWidgets(): array
    {
        return [
            ExtractionBucketResource\Widgets\BucketStatWidget::make([
                'bucketId' => $this->record->id,
                'colorByCount' => $this->colorByCount,
            ]),
            AverageDurationChart::make([
                'bucketId' => $this->record->id,
                'model' => $this->model,
                'colorByCount' => $this->colorByCount,
            ]),
            MedianDurationChart::make([
                'bucketId' => $this->record->id,
                'model' => $this->model,
                'colorByCount' => $this->colorByCount,
            ]),
            AverageTokensChart::make([
                'bucketId' => $this->record->id,
                'model' => $this->model,
                'colorByCount' => $this->colorByCount,
            ]),
            MedianTokensChart::make([
                'bucketId' => $this->record->id,
                'model' => $this->model,
                'colorByCount' => $this->colorByCount,
            ]),
            CorrectnessChart::make([
                'bucketId' => $this->record->id,
                'model' => $this->model,
                'colorByCount' => $this->colorByCount,
            ]),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('')
            ->disabled(false)
            ->schema([
                ModelSelect::make('model')
            ]);
    }

    public function getRelationManagers(): array
    {
        return [
            RunsRelationManager::make([
                'model' => $this->model,
            ]),
        ];
    }
}
