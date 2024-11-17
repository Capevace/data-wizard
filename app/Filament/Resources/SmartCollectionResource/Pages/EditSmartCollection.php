<?php

namespace App\Filament\Resources\SmartCollectionResource\Pages;

use App\Filament\Resources\SmartCollectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSmartCollection extends EditRecord
{
    protected static string $resource = SmartCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
