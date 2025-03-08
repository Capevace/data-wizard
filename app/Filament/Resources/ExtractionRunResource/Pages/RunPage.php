<?php

namespace App\Filament\Resources\ExtractionRunResource\Pages;

use App\Filament\Resources\ExtractionBucketResource\Actions\StartExtractionAction;
use App\Filament\Resources\ExtractionRunResource;
use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsCsvAction;
use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsJsonAction;
use App\Filament\Resources\ExtractionRunResource\Actions\DownloadAsXmlAction;
use App\Filament\Resources\ExtractionRunResource\Actions\DownloadEvaluationResults;
use App\Filament\Resources\ExtractionRunResource\Actions\MarkAsFinishedAction;
use App\Filament\Resources\ExtractionRunResource\Actions\SmartModifyJsonAction;
use App\Filament\Resources\SavedExtractorResource\Actions\OpenEmbeddedRunAction;
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
                ->hasExtractorForm()
                ->label('Restart extraction')
                ->translateLabel()
                ->llm(fn () => $this->record->model)
                ->strategy(fn () => $this->record->strategy)
                ->record(fn () => $this->record->bucket)
                ->extractor(fn () => $this->record->saved_extractor)
                ->bucket(fn () => $this->record->bucket),

            SmartModifyJsonAction::make('smartModifyJson')
                ->jsonSchema(fn () => $this->record->target_schema)
                ->dataToModify(fn () => $this->record->result_json ?? $this->record->partial_result_json),

            ActionGroup::make([])
                ->label('Download')
                ->translateLabel()
                ->size('xl')
                ->icon('heroicon-o-cloud-arrow-down')
                ->dropdownPlacement('bottom-end')
                ->actions([
                    DownloadAsJsonAction::make()
                        ->record($this->getRecord()),

                    DownloadAsCsvAction::make()
                        ->record($this->getRecord()),

                    DownloadAsXmlAction::make()
                        ->record($this->getRecord()),

                    DownloadEvaluationResults::make()
                        ->record($this->getRecord()),
                ]),

            ActionGroup::make([])
                ->dropdownPlacement('bottom-end')
                ->actions([
                    Action::make('toggleDebugMode')
                        ->label(fn () => $this->debugModeEnabled ? __('Disable Debug-Mode') : __('Enable Debug-Mode'))
                        ->translateLabel()
                        ->icon('heroicon-o-code-bracket')
                        ->iconPosition('after')
                        ->color(fn () => $this->debugModeEnabled ? 'success' : 'gray')
                        ->action(function () {
                            $this->debugModeEnabled = ! $this->debugModeEnabled;
                        }),

                    OpenEmbeddedRunAction::make()
                        ->record($this->getRecord()),

                    MarkAsFinishedAction::make()
                        ->record($this->getRecord()),
                ]),
        ];
    }
}
