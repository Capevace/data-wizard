<?php

namespace App\Http\Resources;

use App\Filament\Forms\JsonEditor;
use App\Filament\Resources\CustomExtractorResource\Pages;
use App\Filament\Resources\SavedExtractorResource;
use App\Filament\Resources\SavedExtractorResource\Actions\BulkExportExtractorAction;
use App\Filament\Resources\SavedExtractorResource\Actions\GenerateSchemaAction;
use App\Filament\Resources\SavedExtractorResource\Actions\OpenEmbeddedExtractorAction;
use App\Models\SmartCollection;
use Filament\Forms\Components\Tabs;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SmartCollectionResource extends Resource
{
    protected static ?string $model = SmartCollection::class;

    protected static ?string $slug = 'smart-collections';

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public static function getModelLabel(): string
    {
        return __('Smart Collection');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Smart Collections');
    }

    public static function form(Form $form): Form
    {
        return $form

            ->schema([
                Tabs::make('UI')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Tabs\Tab::make('Metadata')
                            ->icon('heroicon-o-code-bracket')
                            ->iconPosition('after')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->translateLabel()
                                    ->placeholder(__('e.g. Extracting products from brochure PDFs')),

                                TextInput::make('icon')
                                    ->label('LLM Strategy')
                                    ->translateLabel(),

                                TextInput::make('color')
                                    ->label('LLM Strategy')
                                    ->translateLabel(),
                            ]),

                        Tabs\Tab::make('Schema')
                            ->icon('heroicon-o-code-bracket')
                            ->iconPosition('after')
                            ->schema([
                                JsonEditor::make('schema_array')
                                    ->id('data.json_schema_string')
                                    ->required()
                                    ->label('JSON Schema')
                                    ->translateLabel()
                                    ->hintActions([
                                        GenerateSchemaAction::make('generateSchema'),
                                    ]),
                            ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->default('No title')
                    ->color(fn ($state) => $state === 'No title' ? 'gray' : null),

                IconColumn::make('icon')
                    ->label('Icon')
                    ->icon(fn ($state) => view()->exists("components.{$state}")
                        ? $state
                        : null
                    ),

                TextColumn::make('color')
                    ->label('Color')
                    ->color(fn ($state) => match ($state) {
                        'primary' => 'primary',
                        'secondary' => 'secondary',
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger' => 'danger',
                        'info' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                ActionGroup::make([])
                    ->actions([
                        OpenEmbeddedExtractorAction::make(),
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
                    BulkExportExtractorAction::make(),
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
            'edit' => SavedExtractorResource\Pages\EditSavedExtractor::route('/{record}/edit'),
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
