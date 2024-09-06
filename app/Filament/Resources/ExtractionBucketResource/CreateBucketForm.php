<?php

namespace App\Filament\Resources\ExtractionBucketResource;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;

class CreateBucketForm
{
    public static function schema(): array
    {
        return [
            Textarea::make('description')
                ->label('Describe what you want to extract')
                ->columnSpanFull()
                ->translateLabel()
                ->placeholder(__('e.g. Importing paper invoices from 2022-01-01 to 2022-12-31'))
                ->required(),

            SpatieMediaLibraryFileUpload::make('files')
                ->required()
                ->label('Upload your files')
                ->columnSpanFull()
                ->translateLabel()
                ->acceptedFileTypes(['application/pdf', 'image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                ->extraInputAttributes(['class' => 'min-h-64 justify-center flex flex-col'])
                ->multiple(),

            //            Hidden::make('extractor_id')
            //                ->default('default'),
            //
            //            Hidden::make('status')
            //                ->default(BucketStatus::Pending),
            //
            //            Hidden::make('created_by')
            //                ->default(fn () => auth()->user()->id),
            //
            //            Hidden::make('started_at')
            //                ->default(fn () => now()),
        ];
    }
}
