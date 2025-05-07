<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Filament\Pages\Playground;
use App\Models\SavedExtractor;
use Filament\Actions\Action;

class OpenInPlaygroundAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('openInPlayground')
            ->label('Open in Playground')
            ->icon('heroicon-o-sparkles')
            ->url(fn (SavedExtractor $record): string => Playground::getUrl([
                'extractor' => $record->id,
            ]));
    }
}
