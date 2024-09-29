<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtractionRunResource\Pages;
use App\Models\ExtractionRun;
use App\Models\ExtractionRun\RunStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Novadaemon\FilamentPrettyJson\PrettyJson;

class ExtractionRunResource extends Resource
{
    protected static ?string $model = ExtractionRun::class;

    protected static ?string $slug = 'runs';

    protected static ?string $navigationIcon = 'heroicon-o-play';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->options(RunStatus::class)
                    ->required(),

                Select::make('started_by_id')
                    ->relationship('started_by', 'name')
                    ->searchable(),

                //                PrettyJson::make('partial_result_json'),
                PrettyJson::make('result_json')
                    ->columnSpanFull(),
                //                    ->extraInputAttributes(['class' => 'font-mono !text-xs', 'style' => 'font-size: 0.6rem']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('saved_extractor.label')
                    ->label('Extractor')
                    ->translateLabel()
                    ->url(fn (ExtractionRun $record) => $record->saved_extractor_id
                        ? SavedExtractorResource::getUrl('edit', ['record' => $record->saved_extractor_id])
                        : null
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('started_by.name')
                    ->label('Started By')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Started At')
                    ->translateLabel()
                    ->since()
                    ->dateTimeTooltip()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('token_stats')
                    ->label('Tokens')
                    ->translateLabel()
                    ->state(fn (ExtractionRun $record) => $record->token_stats?->tokens)
                    ->description(fn (ExtractionRun $record) => $record->token_stats?->calculateTotalCost()?->format())
                    ->tooltip(fn (ExtractionRun $record) => $record->token_stats?->cost?->formatAveragePer1M())
                    ->searchable()
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('token_stats->tokens', $direction)),

                TextColumn::make('json')
                    ->state(fn (ExtractionRun $record) => count(Arr::get(collect($record->result_json)->first(), 'data.estates') ?? []))
                //
                //                TextColumn::make('partial_result_json')
                //                    ->formatStateUsing(fn(?array $state) => json_encode($state, JSON_PRETTY_PRINT)),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtractionRuns::route('/'),
            'create' => Pages\CreateExtractionRun::route('/create'),
            'view' => ExtractionRunResource\Pages\RunPage::route('/{record}/run'),
            //            'view' => ExtractionBucketResource\Pages\RunPage::route('/{record}'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['startedBy']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['startedBy.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->startedBy) {
            $details['StartedBy'] = $record->startedBy->name;
        }

        return $details;
    }
}
