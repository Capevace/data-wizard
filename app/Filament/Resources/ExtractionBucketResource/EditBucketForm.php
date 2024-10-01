<?php

namespace App\Filament\Resources\ExtractionBucketResource;

use App\Filament\Forms\DataWizardInput;
use App\Models\ExtractionBucket\BucketStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
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
                ->required()
                ->extraInputAttributes(['style' => 'min-height: 10rem']),

            Group::make()
//                ->compact()
                ->columnSpan(1)
                ->schema([
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
                        ->default(fn () => auth()->user()->id)
                ])
        ];
    }
}
