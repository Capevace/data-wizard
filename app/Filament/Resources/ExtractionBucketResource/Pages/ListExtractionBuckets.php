<?php

namespace App\Filament\Resources\ExtractionBucketResource\Pages;

use App\Filament\Resources\ExtractionBucketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtractionBuckets extends ListRecords
{
    protected static string $resource = ExtractionBucketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
