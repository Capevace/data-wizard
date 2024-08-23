<?php

namespace App\Filament\Resources\ExtractionRunResource\Pages;

use App\Filament\Resources\ExtractionRunResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtractionRuns extends ListRecords
{
    protected static string $resource = ExtractionRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
