<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Models\SavedExtractor;
use Filament\Actions\Action;

class OpenEmbeddedExtractorAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'openEmbeddedExtractor';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Open embedded extractor')
            ->translateLabel()
            ->icon('heroicon-o-eye')
            ->url(fn (SavedExtractor $record) => $record->getEmbeddedUrl())
            ->openUrlInNewTab();
    }
}
