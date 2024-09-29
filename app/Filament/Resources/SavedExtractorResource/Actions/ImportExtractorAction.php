<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Exports\ExtractorFileFormat;
use App\Models\SavedExtractor;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImportExtractorAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importExtractor';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(fn (?SavedExtractor $record) => $record === null
                ? __('Import extractor')
                : __('Load extractor')
            )
            ->translateLabel()
            ->icon('bi-upload')
            ->modalDescription(__('Import a previously exported extractor and overwrite the current values.'))
            ->modalIcon('bi-upload')
            ->modalWidth('lg')
            ->modalFooterActionsAlignment('center')
            ->modalSubmitActionLabel(fn (?SavedExtractor $record) =>
                $record === null
                    ? __('Import')
                    : __('Overwrite')
            )
            ->form([
                FileUpload::make('extractor')
                    ->storeFiles(false)
                    ->label('Extractor (.json)')
                    ->translateLabel()
                    ->multiple(false)
                    ->acceptedFileTypes(['application/json'])
                    ->maxSize(1024 * 1024 * 20)
                    ->directory('extractor-imports')
                    ->required(),
            ])
            ->action(function (?SavedExtractor $record, array $data) {
                /** @var ?TemporaryUploadedFile $file */
                $file = $data['extractor'] ?? null;
                $json = $file?->getContent();

                if (empty($json)) {
                    $this
                        ->failureNotificationTitle(__('No extractor provided'))
                        ->failure();

                    return;
                }

                try {
                    ExtractorFileFormat::import($json, update: $record);
                } catch (\Throwable $e) {
                    report($e);

                    $this
                        ->failureNotificationTitle(__('Could not import extractor'))
                        ->failure();

                    return;
                }

                $this
                    ->successNotificationTitle(__('Extractor imported'))
                    ->success();
            });
    }
}
