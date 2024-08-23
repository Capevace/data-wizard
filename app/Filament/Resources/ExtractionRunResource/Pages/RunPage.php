<?php

namespace App\Filament\Resources\ExtractionRunResource\Pages;

use App\Filament\Resources\ExtractionBucketResource;
use App\Filament\Resources\ExtractionRunResource;
use App\Models\ExtractionBucket;
use Filament\Resources\Pages\ViewRecord;

/**
 * @property-read ExtractionBucket $record
 */
class RunPage extends ViewRecord
{
    protected static string $resource = ExtractionRunResource::class;

    protected static string $view = 'filament.pages.run';

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
