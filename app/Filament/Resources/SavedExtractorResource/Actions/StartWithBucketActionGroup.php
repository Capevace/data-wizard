<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Filament\Resources\ExtractionBucketResource\Actions\StartExtractionAction;
use App\Models\ExtractionBucket;
use App\Models\SavedExtractor;
use Filament\Actions\ActionGroup;

class StartWithBucketActionGroup extends ActionGroup
{
    public static function make(array $actions = []): static
    {
        return parent::make($actions);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Start extraction')
            ->translateLabel()
            ->icon('heroicon-o-play')
            ->button()
            ->dropdownWidth('md')
            ->dropdownPlacement('bottom-end')
            ->actions([
                ...(
                    // Show 4 saved extractors
                    ExtractionBucket::query()
                        ->whereNotNull('description')
                        ->limit(4)
                        ->get()
                        ->map(fn (ExtractionBucket $bucket) => StartExtractionAction::make('start'.$bucket->id)
                            ->hasExtractorForm()
                            ->color('primary')
                            ->icon('bi-bucket')
                            ->label(str($bucket->description)->limit(50))
                            ->bucket($bucket)
                            ->extractor(fn (?SavedExtractor $record) => $record)
                        )
                ),
                StartExtractionAction::make('startExtraction')
                    ->hasExtractorForm()
                    ->label('Choose any bucket')
                    ->extractor(fn (?SavedExtractor $record) => $record),
            ]);
    }
}
