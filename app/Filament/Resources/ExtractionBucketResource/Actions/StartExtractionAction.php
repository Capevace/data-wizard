<?php

namespace App\Filament\Resources\ExtractionBucketResource\Actions;

use App\Filament\Resources\ExtractionRunResource;
use App\Jobs\GenerateDataJob;
use App\Models\ExtractionBucket;
use App\Models\SavedExtractor;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class StartExtractionAction extends Action
{
    protected Closure|SavedExtractor|null $extractor = null;
    protected Closure|ExtractionBucket|null $bucket = null;

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

    public function hasExtractorForm(): static
    {
        return $this
            ->label('Choose extractor')
            ->translateLabel()
            ->icon('heroicon-o-cursor-arrow-rays')
            ->color('gray')
            ->modalIconColor('primary')
            ->form([
                Select::make('saved_extractor_id')
                    ->required()
                    ->default(fn () => $this->getExtractor()?->id)
                    ->disabled(fn () => $this->getExtractor() ? true : false)
                    ->label('Saved Extractor')
                    ->translateLabel()
                    ->searchable()
                    ->searchDebounce(200)
                    ->options(fn () =>
                        SavedExtractor::query()
                            ->latest('updated_at')
                            ->limit(10)
                            ->get()
                            ->pluck('label', 'id')
                    )
                    ->getSearchResultsUsing(fn (?string $search) =>
                        SavedExtractor::query()
                            ->when($search, fn ($query) => $query->where('label', 'like', "%{$search}%"))
                            ->latest('updated_at')
                            ->limit(10)
                            ->get()
                            ->pluck('label', 'id')
                    ),

                Select::make('bucket_id')
                    ->default(fn () => $this->getBucket()?->id)
                    ->disabled(fn () => $this->getBucket() ? true : false)
                    ->label('Bucket')
                    ->translateLabel()
                    ->searchable()
                    ->searchDebounce(200)
                    ->options(fn () =>
                        ExtractionBucket::query()
                            ->limit(10)
                            ->get()
                            ->pluck('description', 'id')
                    )
                    ->getSearchResultsUsing(fn (?string $search) =>
                        ExtractionBucket::query()
                            ->when($search, fn ($query) => $query->where('description', 'like', "%{$search}%"))
                            ->limit(10)
                            ->get()
                            ->pluck('description', 'id')
                    )
                    ->required(),

                Fieldset::make('Options')
                    ->columns(1)
                    ->schema([
                        Toggle::make('include_pdf_text')
                            ->label('PDF text')
                            ->helperText('Send the text contents of the PDF to the LLM')
                            ->translateLabel()
                            ->default(true)
                            ->required(),

                        Toggle::make('include_pdf_pages_as_images')
                            ->label('PDF pages as images')
                            ->helperText('Send the screenshots of the PDF pages to the LLM')
                            ->translateLabel()
                            ->default(false)
                            ->required(),

                        Toggle::make('inclide_pdf_images')
                            ->label('PDF raw images')
                            ->helperText('Extract and send the embedded images of the PDF to the LLM')
                            ->translateLabel()
                            ->default(false)
                            ->required(),
                    ])
            ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('Start extraction'))
            ->icon('heroicon-o-play')
            ->color('primary')
            ->requiresConfirmation()
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
                $startedBy = auth()->user();

                if ($extractorId) {
                    $extractor = SavedExtractor::find($extractorId) ?? $extractor;
                }

                if (!$extractor) {
                    $this
                        ->failureNotificationTitle(__('No extractor selected'))
                        ->failure();

                    return;
                }

                if ($bucketId) {
                    $bucket = ExtractionBucket::find($bucketId) ?? $bucket;
                }

                if (!$bucket) {
                    $this
                        ->failureNotificationTitle(__('No bucket selected'))
                        ->failure();

                    return;
                }

                $run = $bucket->runs()->create([
                    'strategy' => $extractor->strategy,
                    'started_by_id' => $startedBy?->id,
                    'target_schema' => $extractor->json_schema,
                    'saved_extractor_id' => $extractor->id,
                ]);

                GenerateDataJob::dispatch(
                    run: $run,
                    startedBy: $startedBy
                );

                $url = ExtractionRunResource::getUrl('view', ['record' => $run->id]);

                $this->redirect($url);
            });
    }
}
