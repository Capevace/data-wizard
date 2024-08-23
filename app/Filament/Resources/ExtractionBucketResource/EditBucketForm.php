<?php

namespace App\Filament\Resources\ExtractionBucketResource;

use App\Models\ExtractionBucket\BucketStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;

class EditBucketForm
{
    public static function schema(): array
    {
        return [
            Textarea::make('description')
                ->translateLabel()
                ->placeholder(__('e.g. Importing paper invoices from 2022-01-01 to 2022-12-31'))
                ->required(),

            Select::make('status')
                ->required()
                ->label('Status')
                ->translateLabel()
                ->searchable()
                ->options(BucketStatus::class)
                ->default(BucketStatus::Pending),

            Select::make('created_by')
                ->label('Created By')
                ->translateLabel()
                ->searchable()
                ->relationship('created_by', 'name')
                ->exists('users', 'id')
                ->uuid()
                ->default(fn () => auth()->user()->id),

            DatePicker::make('started_at')
                ->label('Started At')
                ->translateLabel(),

            Hidden::make('extractor_id')
                ->default('default'),
//                TextInput::make('extractor_id')
//                    ->label('Extractor ID')
//                    ->translateLabel()
//                    ->default('default')
//                    ->required(),

            SpatieMediaLibraryFileUpload::make('files')
                ->columnSpanFull()
                ->extraInputAttributes(['class' => 'min-h-64 justify-center flex flex-col'])
                ->visibleOn('create')
                ->label('Files')
                ->translateLabel()
                ->multiple()
        ];
    }
}
