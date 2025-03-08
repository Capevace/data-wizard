<?php

namespace App\Filament\Resources\ExtractionBucketResource\Pages;

use App\Filament\Resources\ExtractionBucketResource;
use App\Filament\Resources\ExtractionBucketResource\Actions\StartWithExtractorActionGroup;
use App\Models\ExtractionBucket;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * @property-read ExtractionBucket $record
 */
class EditExtractionBucket extends EditRecord
{
    protected static string $resource = ExtractionBucketResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form);
    }

    protected function getSavedNotification(): ?Notification
    {
        return parent::getSavedNotification()
            ->duration(1500);
    }

    public function updatedData()
    {
        $this->save();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label(__('Delete bucket'))
                ->tooltip(__('Delete bucket'))
                ->translateLabel()
                ->icon('heroicon-o-trash')
                ->iconButton()
                ->size('lg')
                ->outlined()
                ->requiresConfirmation(),


            ForceDeleteAction::make()
                ->iconButton(),
            RestoreAction::make()
                ->iconButton(),

            Action::make('evaluate')
                ->label('Evaluate')
                ->tooltip(__('Evaluate'))
                ->translateLabel()
                ->iconButton()
                ->size('lg')
                ->icon('heroicon-o-chart-pie')
                ->url(EvaluateExtractionBucket::getUrl(['record' => $this->record->id])),

            StartWithExtractorActionGroup::make(),

//            ActionGroup::make([
//
//            ])
//                ->dropdownPlacement('bottom-end')
//                ->size('xl')
        ];
    }
}
