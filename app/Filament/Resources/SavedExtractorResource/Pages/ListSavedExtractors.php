<?php

namespace App\Filament\Resources\CustomExtractorResource\Pages;

use App\Filament\Resources\SavedExtractorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSavedExtractors extends ListRecords
{
    protected static string $resource = SavedExtractorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
