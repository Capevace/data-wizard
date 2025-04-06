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
            CreateAction::make('quickCreate')
                ->color('gray')
                ->label(__('Generate Extractor'))
                ->url(SavedExtractorResource::getUrl('quick-create'))
                ->icon('heroicon-o-plus'),
            ImportExtractorAction::make()
                ->color('gray')
        ];
    }
}
