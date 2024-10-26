<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Models\ExtractionRun;
use App\Models\SavedExtractor;
use Filament\Actions\Action;

class OpenEmbeddedRunAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'openEmbeddedRun';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Open Embeddable Extractor')
            ->translateLabel()
            ->icon('heroicon-o-arrow-top-right-on-square')
            ->url(fn (ExtractionRun $record) => $record->getEmbeddedUrl())
            ->openUrlInNewTab();
    }
}
