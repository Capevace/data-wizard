<?php

namespace App\Filament\Resources\CustomExtractorResource\Pages;

use App\Filament\Resources\ExtractionBucketResource\Actions\StartWithExtractorActionGroup;
use App\Filament\Resources\SavedExtractorResource;
use App\Filament\Resources\SavedExtractorResource\Actions\StartWithBucketActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSavedExtractor extends EditRecord
{
    protected static string $resource = SavedExtractorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),

            StartWithBucketActionGroup::make(),
        ];
    }
}
