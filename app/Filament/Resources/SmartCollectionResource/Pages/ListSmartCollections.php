<?php

namespace App\Filament\Resources\SmartCollectionResource\Pages;

use App\Filament\Resources\SmartCollectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmartCollections extends ListRecords
{
    protected static string $resource = SmartCollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
