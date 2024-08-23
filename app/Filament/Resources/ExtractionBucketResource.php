<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Extraction;
use App\Filament\Resources\ExtractionBucketResource\CreateBucketForm;
use App\Filament\Resources\ExtractionBucketResource\EditBucketForm;
use App\Filament\Resources\ExtractionBucketResource\Pages;
use App\Filament\Resources\ExtractionBucketResource\RelationManagers\FilesRelationManager;
use App\Filament\Resources\ExtractionBucketResource\RelationManagers\RunsRelationManager;
use App\Models\ExtractionBucket;
use App\Models\ExtractionBucket\BucketStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtractionBucketResource extends Resource
{
    protected static ?string $model = ExtractionBucket::class;

    protected static ?string $slug = 'buckets';

    protected static ?string $navigationIcon = 'bi-bucket';


    public static function getModelLabel(): string
    {
        return __('Bucket');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(fn (HasForms $livewire) => $livewire instanceof Pages\CreateExtractionBucket
                ? CreateBucketForm::schema()
                : EditBucketForm::schema()
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->translateLabel()
                    ->grow(true),

                TextColumn::make('created_by.name')
                    ->label('Created By')
                    ->translateLabel(),

                TextColumn::make('status')
                    ->translateLabel()
                    ->badge(),

                TextColumn::make('started_at')
                    ->label('Started At')
                    ->translateLabel()
                    ->date(),

                TextColumn::make('extractor_id')
                    ->label('Extractor ID')
                    ->translateLabel(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtractionBuckets::route('/'),
            'create' => Pages\CreateExtractionBucket::route('/create'),
            'edit' => Pages\EditExtractionBucket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }

    public static function getRelations(): array
    {
        return [
            FilesRelationManager::class,
            RunsRelationManager::class,
        ];
    }
}
