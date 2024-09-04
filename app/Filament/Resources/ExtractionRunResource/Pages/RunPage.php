<?php

namespace App\Filament\Resources\ExtractionRunResource\Pages;

use App\Filament\Resources\ExtractionBucketResource;
use App\Filament\Resources\ExtractionBucketResource\Actions\StartExtractionAction;
use App\Filament\Resources\ExtractionRunResource;
use App\Filament\Resources\ExtractionRunResource\Actions\SmartModifyJsonAction;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\Locked;

/**
 * @property-read ExtractionRun $record
 */
class RunPage extends ViewRecord
{
    protected static string $resource = ExtractionRunResource::class;

    protected static string $view = 'filament.pages.run';

    #[Locked]
    public bool $debugModeEnabled = false;

    protected function getHeaderActions(): array
    {
        return [
            StartExtractionAction::make('startExtraction')
                ->label('Restart extraction')
                ->translateLabel()
                ->record(fn () => $this->record->bucket)
                ->extractor(fn () => $this->record->saved_extractor)
                ->bucket(fn () => $this->record->bucket),

            SmartModifyJsonAction::make('smartModifyJson')
                ->jsonSchema(fn () => $this->record->target_schema)
                ->dataToModify(fn () => $this->record->result_json ?? $this->record->partial_result_json),

            ActionGroup::make([])
                ->actions([
                    Action::make('toggleDebugMode')
                        ->label(fn () => $this->debugModeEnabled ? 'Disable debug mode' : 'Enable debug mode')
                        ->translateLabel()
                        ->icon('heroicon-o-code-bracket')
                        ->iconPosition('after')
                        ->color(fn () => $this->debugModeEnabled ? 'success' : 'gray')
                        ->action(function () {
                            $this->debugModeEnabled = !$this->debugModeEnabled;
                        }),
                ])
        ];
    }
}
