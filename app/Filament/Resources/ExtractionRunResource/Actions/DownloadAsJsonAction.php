<?php

namespace App\Filament\Resources\ExtractionRunResource\Actions;

use App\Models\ExtractionRun;
use Filament\Actions\Action;
use Illuminate\Support\Arr;
use League\Csv\Writer;

class DownloadAsJsonAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('downloadAsJson')
            ->label('Download as JSON')
            ->translateLabel()
            ->icon('bi-filetype-json')
            ->color('gray')
            ->action(function (ExtractionRun $record) {
                // JSON Schema. Will be an object schema (type=object)
                $data = $record->result_json ?? $record->partial_result_json;

                $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                return response()->streamDownload(function () use ($content) {
                    echo $content;
                }, 'data.json');
            });
    }
}
