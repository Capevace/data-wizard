<?php

namespace App\Filament\Resources\SavedExtractorResource\Pages;

use App\Filament\Resources\SavedExtractorResource;
use App\Filament\Resources\SavedExtractorResource\Actions\ConfigureIFrameAction;
use App\Filament\Resources\SavedExtractorResource\Actions\ExportExtractorAction;
use App\Filament\Resources\SavedExtractorResource\Actions\GenerateStepLabelsAction;
use App\Filament\Resources\SavedExtractorResource\Actions\ImportExtractorAction;
use App\Filament\Resources\SavedExtractorResource\Actions\OpenEmbeddedExtractorAction;
use App\Filament\Resources\SavedExtractorResource\Actions\StartWithBucketActionGroup;
use App\Filament\Resources\SavedExtractorResource\Actions\TestWebhookAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSavedExtractor extends EditRecord
{
    protected static string $resource = SavedExtractorResource::class;


    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function updatedData()
    {
        $this->save();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),

            StartWithBucketActionGroup::make(),

            ActionGroup::make([
                ConfigureIFrameAction::make(),
                GenerateStepLabelsAction::make(),
                OpenEmbeddedExtractorAction::make(),
                ExportExtractorAction::make(),
                ImportExtractorAction::make(),
                TestWebhookAction::make(),
            ])
        ];
    }
}
