<?php

namespace App\Filament\Resources;

use App\Filament\Forms\JsonEditor;
use App\Filament\Resources\CustomExtractorResource\Pages;
use App\Filament\Resources\SavedExtractorResource\Actions\GenerateSchemaAction;
use App\Models\SavedExtractor;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\ReplicateAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SavedExtractorResource extends Resource
{
    protected static ?string $model = SavedExtractor::class;

    protected static ?string $slug = 'saved-extractors';

    protected static ?string $navigationIcon = 'bi-robot';

    public static function getModelLabel(): string
    {
        return __('Saved Extractor');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Saved Extractors');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('was_automatically_created')
                    ->default(false),

                TextInput::make('label')
                    ->label('Label')
                    ->translateLabel()
                    ->placeholder(__('e.g. Extracting products from brochure PDFs')),

                Select::make('strategy')
                    ->label('LLM Strategy')
                    ->translateLabel()
                    ->selectablePlaceholder(false)
                    ->default('simple')
                    ->options([
                        'simple' => __('Simple'),
                        'sequential' => __('Sequential'),
                        'parallel' => __('Parallel'),
                    ])
                    ->required(),

                JsonEditor::make('json_schema')
                    ->required()
                    ->label('JSON Schema')
                    ->translateLabel()
                    ->hintActions([
                        GenerateSchemaAction::make('generateSchema'),
                    ]),

                Textarea::make('output_instructions')
                    ->label('LLM Output Instructions')
                    ->translateLabel()
                    ->placeholder(__('example-output-instructions'))
                    ->autosize()
                    ->extraAttributes(['class' => 'h-full', 'style' => 'min-height: 26rem'])
                    ->helperText(__('It is recommended to keep the output instructions short and concise to save LLM tokens. You can write prompts in other languages, but English is recommended.')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->default('No label')
                    ->color(fn ($state) => $state === 'No label' ? 'gray' : null),

                TextColumn::make('output_instructions')
                    ->grow(false)
                    ->wrap()
                    ->limit(200),

                TextColumn::make('strategy'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([])
                    ->actions([
                        EditAction::make(),
                        ReplicateAction::make()
                            ->requiresConfirmation(),
                        DeleteAction::make(),
                        RestoreAction::make(),
                        ForceDeleteAction::make(),
                    ]),
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
            'index' => Pages\ListSavedExtractors::route('/'),
            'create' => Pages\CreateSavedExtractor::route('/create'),
            'edit' => Pages\EditSavedExtractor::route('/{record}/edit'),
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
}
