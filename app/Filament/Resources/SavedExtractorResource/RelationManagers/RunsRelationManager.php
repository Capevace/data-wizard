<?php

namespace App\Filament\Resources\SavedExtractorResource\RelationManagers;

use App\Filament\Resources\ExtractionRunResource;
use App\Models\ExtractionRun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RunsRelationManager extends RelationManager
{
    protected static string $relationship = 'runs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('created_at')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->columns(ExtractionRunResource::table($table)->getColumns())
            ->emptyStateHeading(__('Extractor was not run yet'))
            ->emptyStateIcon('heroicon-o-play')
            ->emptyStateDescription(__('Create a new run to start extracting data.'))
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (ExtractionRun $record) => $record->getAdminUrl()),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
