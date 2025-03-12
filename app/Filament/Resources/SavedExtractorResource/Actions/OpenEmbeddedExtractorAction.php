<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Livewire\Components\EmbeddedExtractor\WidgetAlignment;
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
            ->label('Launch embeded extractor')
            ->translateLabel()
            ->icon('heroicon-o-play')
            ->url(fn (SavedExtractor $record) => $record->getEmbeddedUrl(horizontalAlignment: WidgetAlignment::Center, verticalAlignment: WidgetAlignment::Center))
            ->openUrlInNewTab();
    }
}
