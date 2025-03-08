<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtractionBucketResource\CreateBucketForm;
use App\Filament\Resources\ExtractionBucketResource\EditBucketForm;
use App\Filament\Resources\ExtractionBucketResource\Pages;
use App\Filament\Resources\ExtractionBucketResource\RelationManagers\FilesRelationManager;
use App\Filament\Resources\ExtractionBucketResource\RelationManagers\RunsRelationManager;
use App\Models\ExtractionBucket;
use App\Models\ExtractionBucket\BucketCreationSource;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mateffy\Magic\Chat\ToolWidget;

class ExtractionBucketResource extends Resource
{
    protected static ?string $model = ExtractionBucket::class;

    protected static ?string $slug = 'buckets';

    protected static ?string $navigationIcon = 'bi-bucket';

    public static function getModelLabel(): string
    {
        return __('Bucket');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Buckets');
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
                    ->grow(true)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('files_count')
                    ->label('Number of files')
                    ->translateLabel()
                    ->counts('files')
                    ->formatStateUsing(fn (int $state) => __(":value files", ['value' => $state]))
                    ->sortable(),

                TextColumn::make('created_by.name')
                    ->label('Created By')
                    ->translateLabel()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created at')
                    ->translateLabel()
                    ->date()
                    ->sortable(),

                TextColumn::make('created_using')
                    ->label('Created using')
                    ->translateLabel()
                    ->badge()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('created_using')
                    ->label('Created using')
                    ->translateLabel()
                    ->multiple()
                    ->default([BucketCreationSource::App->value])
                    ->options(BucketCreationSource::class),
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getMagicTools(): array
    {
        return [
            'index' => function (?string $search = null, int $limit = 10, int $offset = 0) {
                $buckets = ExtractionBucket::query()
                    ->when($search, fn ($query) => $query->where('description', 'ilike', "%{$search}%"))
                    ->latest('created_at')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
            }
        ];
    }

    public static function getMagicToolWidgets(): array
    {
        return [
            'index' => ToolWidget::livewire(
                Pages\ListExtractionBucketsInline::class,

            )
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtractionBuckets::route('/'),
            'create' => Pages\CreateExtractionBucket::route('/create'),
            'edit' => Pages\EditExtractionBucket::route('/{record}/edit'),
            'analyze' => Pages\EvaluateExtractionBucket::route('/{record}/evaluate'),
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
