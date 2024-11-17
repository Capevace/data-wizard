<?php

namespace App\Filament\Resources\SavedExtractorResource\Pages;

use App\Filament\Resources\SavedExtractorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSavedExtractor extends CreateRecord
{
    protected static string $resource = SavedExtractorResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
