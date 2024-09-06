<?php

namespace App\Filament\Resources\ExtractionBucketResource\Actions;

use App\Models\ExtractionBucket;
use App\Models\SavedExtractor;
use Filament\Actions\ActionGroup;

class StartWithExtractorActionGroup extends ActionGroup
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
                    SavedExtractor::query()
                        ->whereNotNull('label')
                        ->limit(4)
                        ->get()
                        ->map(fn (SavedExtractor $savedExtractor) => StartExtractionAction::make('start'.$savedExtractor->id)
                            ->label($savedExtractor->label)
                            ->extractor($savedExtractor)
                            ->bucket(fn (?ExtractionBucket $record) => $record)
                        )
                ),
                StartExtractionAction::make('startExtraction')
                    ->hasExtractorForm()
                    ->bucket(fn (?ExtractionBucket $record) => $record),
            ]);
    }
}
