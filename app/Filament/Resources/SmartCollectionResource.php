<?php

namespace App\Filament\Resources;

use Aerni\Spotify\Facades\SpotifyFacade as Spotify;
use App\Filament\Forms\JsonEditor;
use App\Filament\Pages\ItemsPage;
use App\Filament\Resources\SavedExtractorResource\Actions\GenerateSchemaAction;
use App\Filament\Resources\SmartCollectionResource\Pages;
use App\Models\SmartCollection;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;

class SmartCollectionResource extends Resource
{
    protected static ?string $model = SmartCollection::class;

    protected static ?string $slug = 'smart-collections';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                                    ->label('Icon')
                                    ->translateLabel(),

                                TextInput::make('color')
                                    ->label('Farbe')
                                    ->translateLabel(),
                            ]),

                        Tabs\Tab::make('Schema')
                            ->icon('heroicon-o-code-bracket')
                            ->iconPosition('after')
                            ->schema([
                                JsonEditor::make('json_schema_array')
                                    ->columnSpanFull()
                                    ->id('data.json_schema_array')
                                    ->required()
                                    ->label('JSON Schema')
                                    ->translateLabel()
                                    ->hintActions([
                                        GenerateSchemaAction::make('generateSchema')
                                            ->schemaStatePath('data.json_schema_array'),
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
                Action::make('import-playlist')
                    ->label('Import Playlist')
                    ->form([
                        TextInput::make('id')
                            ->label('Playlist ID')
                            ->required(),
                    ])
                    ->action(function (array $data, SmartCollection $record) {
                        $playlist = Spotify::playlist($data['id'])->get();

                        $items = collect(Arr::get($playlist, 'tracks.items', []))
                            ->map(fn (array $item) => [
                                'title' => Arr::get($item, 'track.name', 'Unnamed track'),
                                'data' => Arr::get($item, 'track', []),
                            ])
                            ->all();

                        $record->items()->createMany($items);
                    }),
                Action::make('items')
                    ->label('Items')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (SmartCollection $record) => ItemsPage::getUrl(['collectionId' => $record->id])),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->recordAction('items')
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
            'index' => Pages\ListSmartCollections::route('/'),
            'create' => Pages\CreateSmartCollection::route('/create'),
            'edit' => Pages\EditSmartCollection::route('/{record}/edit'),
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
