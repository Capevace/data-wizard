<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Exports\ExtractorFileFormat;
use App\Models\SavedExtractor;
use Filament\Actions\Action;

class ExportExtractorAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportExtractor';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Export extractor')
            ->translateLabel()
            ->icon('bi-download')
            ->action(function (SavedExtractor $record) {
                $json = ExtractorFileFormat::export($record);

                return response()->streamDownload(function () use ($json) {
                    echo $json;
                }, $record->label.'.json');
            });
    }
}
