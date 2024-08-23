<?php

namespace App\Filament\Resources\ExtractionRunResource\Pages;

use App\Filament\Resources\ExtractionRunResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExtractionRun extends CreateRecord
{
    protected static string $resource = ExtractionRunResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
