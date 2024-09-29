<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Exports\ExtractorFileFormat;
use App\Models\SavedExtractor;
use Filament\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Collection;

class BulkExportExtractorAction extends BulkAction
{
    public static function getDefaultName(): ?string
    {
        return 'bulkExportExtractor';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Export extractors')
            ->translateLabel()
            ->icon('bi-download')
            ->action(function (Collection $records) {
                // Create a zip file
                $tempFile = tempnam(sys_get_temp_dir(), 'extractors');
                $zip = new \ZipArchive();
                $zip->open($tempFile, \ZipArchive::CREATE);

                foreach ($records as $record) {
                    /** @var SavedExtractor $record */

                    $zip->addFromString(
                        $record->label . '.json',
                        ExtractorFileFormat::export($record)
                    );
                }

                $zip->close();

                return response()->download($tempFile, 'extractors.zip');
            });
    }
}
