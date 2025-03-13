<?php

namespace App\Filament\Resources\ExtractionBucketResource\Actions;

use App\Filament\Forms\ModelSelect;
use App\Filament\Forms\SavedExtractorSelect;
use App\Filament\Forms\StrategySelect;
use App\Filament\Resources\ExtractionRunResource;
use App\Filament\Resources\SavedExtractorResource;
use App\Jobs\GenerateDataJob;
use App\Models\ExtractionBucket;
use App\Models\SavedExtractor;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Support\Collection;
use Mateffy\Magic;
use Mateffy\Magic\Extraction\EvaluationType;

class StartEvaluationRuns extends Action
{
    protected Closure|SavedExtractor|null $extractor = null;

    protected Closure|ExtractionBucket $bucket;

    public function extractor(Closure|SavedExtractor|null $extractor): static
    {
        $this->extractor = $extractor;

        return $this;
    }

    public function bucket(Closure|ExtractionBucket|null $bucket): static
    {
        $this->bucket = $bucket;

        return $this;
    }

    public function getExtractor(): Closure|SavedExtractor|null
    {
        return $this->evaluate($this->extractor);
    }

    public function getBucket(): Closure|ExtractionBucket|null
    {
        return $this->evaluate($this->bucket);
    }


    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('startEvaluationRuns')
            ->label(__('Start evaluation runs'))
            ->icon('heroicon-o-play')
            ->modalIcon('heroicon-o-beaker')
            ->iconPosition('after')
            ->modalWidth('2xl')
            ->fillForm(fn () => [
                'model' => $this->getExtractor()?->model ?? Magic::defaultModelName(),
                'saved_extractor_id' => $this->getExtractor()?->id ?? null,
                'count' => 2,
                'evaluation_types' => [EvaluationType::TextOnly, EvaluationType::EmbeddedImages, EvaluationType::PageImages, EvaluationType::All],
            ])
            ->form([
                ModelSelect::make('model')
                    ->live()
                    ->required(),

                SavedExtractorSelect::make('saved_extractor_id')
                    ->live()
                    ->required(),

                TextInput::make('chunk_size')
                    ->label('Chunk size (in tokens)')
                    ->translateLabel()
                    ->placeholder(fn () => $this->getExtractor()?->chunk_size ?? config('llm-magic.artifacts.default_max_tokens'))
                    ->suffix(__('tokens'))
                    ->numeric()
                    ->step(1)
                    ->minValue(SavedExtractorResource::MIN_CHUNK_SIZE),

                TextInput::make('count')
                    ->label('Run count')
                    ->required()
                    ->translateLabel()
                    ->numeric()
                    ->minValue(1)
                    ->live()
                    ->helperText(__('Be careful when increasing the count, as this will drastically increase the number of runs but can be useful for getting statistical significance.')),

                Select::make('evaluation_types')
                    ->label('Evaluation types')
                    ->translateLabel()
                    ->options(EvaluationType::class)
                    ->multiple()
                    ->minItems(1)
                    ->required(),

                StrategySelect::make('strategies')
                    ->multiple()
                    ->minItems(1)
                    ->required(),

                Placeholder::make('total_count')
                    ->label('Total number of runs')
                    ->extraAttributes(['class' => '!text-lg'])
                    ->content(fn (Get $get) => __(':count runs', [
                        'count' => $this->getTotalRunCount(
                            count: $get('count') ?? 1,
                            evaluation_types: $get('evaluation_types') ?? [],
                            strategies: $get('strategies') ?? []
                        )
                    ])),
            ])
            ->color('primary')
            ->modalDescription(__('This will start a set of runs for all different kinds of evaluations. This may be expensive, depending on the model and the size of the data.'))
            ->modalSubmitAction(fn (StaticAction $action) => $action
                ->label('Start')
                ->translateLabel()
                ->icon('heroicon-o-play')
                ->iconPosition('after')
                ->color('primary')
            )
            ->action(function (array $data) {
                $extractor = SavedExtractor::findOrFail($data['saved_extractor_id']);
                $bucket = $this->getBucket();
                $model = $data['model'] ?? $extractor?->model ?? Magic::defaultModelName();
                $startedBy = auth()->user();

                $strategies = $data['strategies'];

                /**
                 * @var Collection<EvaluationType> $evaluation_types
                 */
                $evaluation_types = collect($data['evaluation_types']);

                $chunk_size = ($data['chunk_size'] ?? null)
                    ? intval($data['chunk_size'])
                    : $extractor->chunk_size ?? config('llm-magic.artifacts.default_max_tokens');

                foreach (range(1, $data['count']) as $i) {
                    foreach ($strategies as $strategy) {
                        foreach ($evaluation_types as $type) {
                            $options = $type->toOptions(markEmbeddedImages: false, markPageImages: false);

                            $run = $bucket->runs()->create([
                                'strategy' => $strategy,
                                'started_by_id' => $startedBy?->id,
                                'target_schema' => $extractor->json_schema,
                                'saved_extractor_id' => $extractor->id,
                                'model' => $model,
                                'include_text' => $options->includeText,
                                'include_embedded_images' => $options->includeEmbeddedImages,
                                'mark_embedded_images' => $options->markEmbeddedImages,
                                'include_page_images' => $options->includePageImages,
                                'mark_page_images' => $options->markPageImages,
                                'chunk_size' => $chunk_size
                            ]);

                            GenerateDataJob::dispatch(run: $run);
                        }
                    }
                }
            });
    }

    protected function getTotalRunCount(int $count, array $evaluation_types, array $strategies): int
    {
        $strategies = collect($strategies);

        /**
         * @var Collection<EvaluationType> $evaluation_types
         */
        $evaluation_types = collect($evaluation_types);

        return $strategies->count() * $evaluation_types->count() * $count;
    }
}
