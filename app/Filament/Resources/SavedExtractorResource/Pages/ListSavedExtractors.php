<?php

namespace App\Filament\Resources\SavedExtractorResource\Pages;

use App\Filament\Resources\SavedExtractorResource;
use App\Filament\Resources\SavedExtractorResource\Actions\ImportExtractorAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSavedExtractors extends ListRecords
{
    protected static string $resource = SavedExtractorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-o-plus'),
            ImportExtractorAction::make()
        ];
    }
}
