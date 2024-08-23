<?php

namespace App\Filament\Resources\ExtractionBucketResource\Pages;

use App\Filament\Resources\ExtractionBucketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExtractionBucket extends CreateRecord
{
    protected static string $resource = ExtractionBucketResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
