<?php

namespace App\Filament\Resources\ExtractionBucketResource\Pages;

use App\Filament\Resources\ExtractionBucketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtractionBucketsInline extends ListRecords
{
    public static string $resource = ExtractionBucketResource::class;

    protected static string $view = 'filament.pages.list-records-inline';

    protected function getHeaderActions(): array
    {
//        dd($this->tableSearch);

        return [
            CreateAction::make(),
        ];
    }
}
