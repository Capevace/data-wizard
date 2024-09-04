<?php

namespace App\Filament\Resources\ExtractionBucketResource\Pages;

use App\Filament\Resources\ExtractionBucketResource;
use App\Filament\Resources\ExtractionBucketResource\Actions\StartExtractionAction;
use App\Filament\Resources\ExtractionBucketResource\Actions\StartWithExtractorActionGroup;
use App\Filament\Resources\ExtractionRunResource;
use App\Jobs\GenerateDataJob;
use App\Models\ExtractionBucket;
use App\Models\ExtractionRun;
use App\Models\SavedExtractor;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Select;
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
                ->label('Delete files')
                ->translateLabel()
                ->icon('heroicon-o-trash')
                ->outlined()
                ->requiresConfirmation(),
            ForceDeleteAction::make(),
            RestoreAction::make(),

            StartWithExtractorActionGroup::make(),
        ];
    }
}
