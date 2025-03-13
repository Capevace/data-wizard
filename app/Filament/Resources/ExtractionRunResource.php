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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mateffy\Magic;
use Mateffy\Magic\Extraction\EvaluationType;
use Novadaemon\FilamentPrettyJson\PrettyJson;

class ExtractionRunResource extends Resource
{
    protected static ?string $model = ExtractionRun::class;

    protected static ?string $slug = 'runs';

    protected static ?string $navigationIcon = 'heroicon-o-play';

    public static function getModelLabel(): string
    {
        return __('Run');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Runs');
    }

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
                PrettyJson::make('result_json')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('5s')
            ->defaultSort('created_at', direction: 'desc')
            ->columns([
                TextColumn::make('saved_extractor.label')
                    ->label('Extractor')
                    ->limit(50)
                    ->translateLabel()
                    ->description(fn (ExtractionRun $record) => $record->model)
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

                TextColumn::make('strategy')
                    ->label('Strategy')
                    ->translateLabel()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (string $state) => Str::title($state))
                    ->description(fn (ExtractionRun $record) => $record->getContextOptions()->getEvaluationTypeLabel()),

                TextColumn::make('chunk_size')
                    ->label('Chunk Size')
                    ->translateLabel()
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn (string $state) => Str::title($state))
                    ->description(fn (ExtractionRun $record) => $record->getContextOptions()->getEvaluationTypeLabel()),

                TextColumn::make('formatted_duration')
                    ->label('Duration')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('started_by.name')
                    ->label('Started By')
                    ->toggledHiddenByDefault()
                    ->toggleable()
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('token_stats')
                    ->label('Tokens')
                    ->translateLabel()
                    ->state(function (ExtractionRun $record) {
                        $stats = $record->getEnrichedTokenStats();
                        $inputCost = $stats->calculateInputCost();
                        $outputCost = $stats->calculateOutputCost();

                        return collect([
                            "Input: {$stats->inputTokens} ({$inputCost->format()})",
                            "Output: {$stats->outputTokens} ({$outputCost->format()})",
                        ]);
                    })
                    ->listWithLineBreaks()
                    ->description(fn (ExtractionRun $record) => $record->token_stats?->calculateTotalCost()?->format())
                    ->tooltip(fn (ExtractionRun $record) => $record->token_stats?->cost?->formatAveragePer1M())
                    ->searchable()
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('token_stats->tokens', $direction)),

                TextColumn::make('test')
                    ->label('Test')
                    ->translateLabel()
                    ->listWithLineBreaks()
                    ->state(function (ExtractionRun $run) {
                        $products = collect(Arr::get($run->data, 'real_estate_property.units', []));

                        $result = [
                            'count' => count($products),
                        ];

                        return collect($result)
                            ->map(fn ($value, $key) => "{$key}: {$value}");
                    }),

                TextColumn::make('bucket')
                    ->label('Bucket')
                    ->translateLabel()
                    ->formatStateUsing(fn (ExtractionRun $record) => $record->bucket->description ?? $record->bucket->id)
                    ->toggleable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Started At')
                    ->translateLabel()
                    ->since()
                    ->dateTimeTooltip()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(RunStatus::class),

                SelectFilter::make('model')
                    ->label('Model')
                    ->options(fn () => Magic::models())
                    ->modifyQueryUsing(fn (Builder $query, $state) => $state['value'] ? $query->where('model', $state['value']) : $query),

                SelectFilter::make('strategy')
                    ->label('Strategy')
                    ->options(fn () => DB::table('extraction_runs')->distinct('strategy')->pluck('strategy', 'strategy')->map(fn ($value) => Str::title($value))->toArray()),

                SelectFilter::make('bucket')
                    ->relationship('bucket', 'description')
                    ->searchable()
                    ->preload()
                    ->label('Buckets'),

                SelectFilter::make('evaluation_type')
                    ->label('Evaluation Type')
                    ->options(
                        collect(EvaluationType::cases())
                            ->mapWithKeys(fn (EvaluationType $type) => [$type->value => $type->getLabel()])
                            ->toArray()
                    )
                    ->placeholder('Ignore')
                    ->modifyQueryUsing(fn (Builder $query, $state) => ($type = EvaluationType::tryFrom($state['value'] ?? ''))
                        ? match ($type) {
                            EvaluationType::All => $query
                                ->where('include_text', true)
                                ->where('include_embedded_images', true)
                                ->where('include_page_images', true),
                            EvaluationType::TextOnly => $query
                                ->where('include_text', true)
                                ->where('include_embedded_images', false)
                                ->where('include_page_images', false),
                            EvaluationType::EmbeddedImages => $query
                                ->where('include_text', true)
                                ->where('include_embedded_images', true)
                                ->where('include_page_images', false),
                            EvaluationType::PageImages => $query
                                ->where('include_text', true)
                                ->where('include_embedded_images', false)
                                ->where('include_page_images', true),
                        }
                        : $query
                    ),
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
