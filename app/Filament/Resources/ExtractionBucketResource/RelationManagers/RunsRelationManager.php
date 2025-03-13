<?php

namespace App\Filament\Resources\ExtractionBucketResource\RelationManagers;

use App\Filament\Resources\ExtractionBucketResource\Actions\BulkRetryExtractionAction;
use App\Filament\Resources\ExtractionBucketResource\Actions\QuickViewRunAction;
use App\Filament\Resources\ExtractionRunResource;
use App\Models\ExtractionRun;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class RunsRelationManager extends RelationManager
{
    protected static string $relationship = 'runs';

    #[Reactive]
    public ?string $model = null;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('created_At')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    #[On('setRunFilter')]
    public function setRunFilter(string $strategy, string $context): void
    {
        Arr::set($this->tableFilters, 'evaluation_type.value', $context);
        Arr::set($this->tableFilters, 'strategy.value', $strategy);
    }

    #[On('setModel')]
    public function setModel(): void
    {
        Arr::set($this->tableFilters, 'model.value', $this->model);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('Only completed runs are considered in the statistics.')
            ->recordTitleAttribute('created_At')
            ->columns(ExtractionRunResource::table($table)->getColumns())
            ->recordUrl(null)
            ->recordAction('quickView')
            ->actions([
                QuickViewRunAction::make(),
                Tables\Actions\ViewAction::make()
                    ->url(fn (ExtractionRun $record) => ExtractionRunResource::getUrl('view', ['record' => $record->id])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkRetryExtractionAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
