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
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Mateffy\Magic;

class StartExtractionAction extends Action
{
    protected Closure|SavedExtractor|null $extractor = null;

    protected Closure|ExtractionBucket|null $bucket = null;

    protected Closure|string|null $llm = null;
    protected Closure|string|null $strategy = null;

    public function llm(Closure|string|null $llm): static
    {
        $this->llm = $llm;

        return $this;
    }

    public function strategy(Closure|string|null $strategy): static
    {
        $this->strategy = $strategy;

        return $this;
    }


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

    public function getLlm(): ?string
    {
        return $this->evaluate($this->llm);
    }

    public function getStrategy(): ?string
    {
        return $this->evaluate($this->strategy);
    }

    public function hasExtractorForm(): static
    {
        return $this
            ->label('Choose extractor')
            ->translateLabel()
            ->icon('heroicon-o-cursor-arrow-rays')
            ->color('gray');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('Start extraction'))
            ->icon('heroicon-o-play')
            ->color('primary')
            ->modalIconColor('primary')
            ->modalIcon('bi-robot')
            ->modalWidth('xl')
            ->form([
                SavedExtractorSelect::make('saved_extractor_id')
                    ->default(fn () => $this->getExtractor()?->id)
                    ->hidden(fn () => $this->getExtractor() ? true : false),

                Select::make('bucket_id')
                    ->default(fn () => $this->getBucket()?->id)
                    ->hidden(fn () => $this->getBucket() ? true : false)
                    ->label('Bucket')
                    ->translateLabel()
                    ->searchable()
                    ->searchDebounce(200)
                    ->options(fn () => ExtractionBucket::query()
                        ->limit(10)
                        ->get()
                        ->pluck('description', 'id')
                    )
                    ->getSearchResultsUsing(fn (?string $search) => ExtractionBucket::query()
                        ->when($search, fn ($query) => $query->where('description', 'like', "%{$search}%"))
                        ->limit(10)
                        ->get()
                        ->pluck('description', 'id')
                    )
                    ->required(),

                StrategySelect::make('strategy')
                    ->default(fn () => $this->getStrategy() ?? $this->getExtractor()?->strategy)
                    ->required(),

                ModelSelect::make('model')
                    ->default(fn () => $this->getLlm() ?? $this->getExtractor()?->model)
                    ->required(),

                Fieldset::make('Options')
                    ->columns(1)
                    ->schema([
                        TextInput::make('chunk_size')
                            ->label('Chunk size (in tokens)')
                            ->translateLabel()
                            ->placeholder(fn () => $this->getExtractor()?->chunk_size ?? config('llm-magic.artifacts.default_max_tokens'))
                            ->suffix(__('tokens'))
                            ->numeric()
                            ->step(1)
                            ->minValue(SavedExtractorResource::MIN_CHUNK_SIZE),

                        Toggle::make('include_text')
                            ->default(fn () => $this->getExtractor()?->include_text ?? true)
                            ->label('Include text')
                            ->helperText('Include the raw text of the document')
                            ->translateLabel()
                            ->onIcon('bi-fonts')
                            ->onColor('success')
                            ->offIcon('heroicon-o-x-mark')
                            ->offColor('danger')
                            ->required(),

                        Toggle::make('include_embedded_images')
                            ->default(fn () => $this->getExtractor()?->include_embedded_images ?? true)
                            ->label('Include embedded images')
                            ->helperText('Include the images embedded in the document')
                            ->live()
                            ->translateLabel()
                            ->onIcon('bi-image')
                            ->onColor('success')
                            ->offIcon('heroicon-o-x-mark')
                            ->offColor('danger')
                            ->required(),

                        Toggle::make('mark_embedded_images')
                            ->default(fn () => $this->getExtractor()?->mark_embedded_images ?? true)
                            ->label('Mark images with identifiers')
                            ->helperText('Draws text labels onto the embedded images for the LLM to reference in the output')
                            ->translateLabel()
                            ->onIcon('bi-123')
                            ->onColor('success')
                            ->offIcon('heroicon-o-x-mark')
                            ->offColor('danger')
                            ->hidden(fn (Get $get) => ! $get('include_embedded_images'))
                            ->required(),

                        Toggle::make('include_page_images')
                            ->default(fn () => $this->getExtractor()?->include_page_images ?? false)
                            ->label('Include PDF screenshots')
                            ->helperText('Include screenshots of each of the pages')
                            ->live()
                            ->translateLabel()
                            ->onIcon('bi-file-earmark-image')
                            ->onColor('success')
                            ->offIcon('heroicon-o-x-mark')
                            ->offColor('danger')
                            ->required(),

                        Toggle::make('mark_page_images')
                            ->default(fn () => $this->getExtractor()?->mark_page_images ?? true)
                            ->label('Mark embedded images')
                            ->helperText('Draw a rectangle and label around embedded images directly onto the page')
                            ->hidden(fn (Get $get) => ! $get('include_page_images'))
                            ->translateLabel()
                            ->onIcon('heroicon-o-paint-brush')
                            ->onColor('success')
                            ->offIcon('heroicon-o-x-mark')
                            ->offColor('danger')
                            ->required(),
                    ]),
            ])
            ->modalDescription(__('This action will cost money. Are you sure?'))
            ->modalSubmitAction(fn (StaticAction $action) => $action
                ->label('Start extraction')
                ->translateLabel()
                ->icon('heroicon-o-play')
                ->color('primary')
            )
            ->action(function (array $data) {
                $extractor = $this->getExtractor();
                $bucket = $this->getBucket();
                $extractorId = $data['saved_extractor_id'] ?? null;
                $bucketId = $data['bucket_id'] ?? null;
                $model = $data['model'] ?? $this->getLlm() ?? $extractor?->model ?? Magic::defaultModelName();
                $strategy = $data['strategy'] ?? $this->getStrategy() ?? $extractor?->strategy;
                $startedBy = auth()->user();

                if ($extractorId) {
                    $extractor = SavedExtractor::find($extractorId) ?? $extractor;
                }

                if (! $extractor) {
                    $this
                        ->failureNotificationTitle(__('No extractor selected'))
                        ->failure();

                    return;
                }

                if ($bucketId) {
                    $bucket = ExtractionBucket::find($bucketId) ?? $bucket;
                }

                if (! $bucket) {
                    $this
                        ->failureNotificationTitle(__('No bucket selected'))
                        ->failure();

                    return;
                }

                $chunk_size = ($data['chunk_size'] ?? null)
                    ? intval($data['chunk_size'])
                    : $extractor->chunk_size ?? config('llm-magic.artifacts.default_max_tokens');

                $run = $bucket->runs()->create([
                    'strategy' => $strategy,
                    'started_by_id' => $startedBy?->id,
                    'target_schema' => $extractor->json_schema,
                    'saved_extractor_id' => $extractor->id,
                    'model' => $model,
                    'include_text' => $data['include_text'] ?? $extractor->include_text,
                    'include_embedded_images' => $data['include_embedded_images'] ?? $extractor->include_embedded_images,
                    'mark_embedded_images' => $data['mark_embedded_images'] ?? $extractor->mark_embedded_images,
                    'include_page_images' => $data['include_page_images'] ?? $extractor->include_page_images,
                    'mark_page_images' => $data['mark_page_images'] ?? $extractor->mark_page_images,
                    'chunk_size' => $chunk_size
                ]);

                GenerateDataJob::dispatch(run: $run);

                $url = ExtractionRunResource::getUrl('view', ['record' => $run->id]);

                $this->redirect($url);
            });
    }
}
