<?php

namespace App\Filament\Resources;

use App\Filament\Forms\JsonEditor;
use App\Filament\Resources\CustomExtractorResource\Pages;
use App\Filament\Resources\SavedExtractorResource\Actions\GenerateSchemaAction;
use App\Filament\Resources\SavedExtractorResource\Actions\OpenEmbeddedExtractorAction;
use App\Filament\Resources\SavedExtractorResource\StepLabelsForm;
use App\Models\SavedExtractor;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Forms\Get;
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
            ->live(onBlur: true)
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
                    ->id('data.json_schema_string')
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


                Tabs::make('UI')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Tabs\Tab::make('Introduction')
                            ->badge(1)
                            ->schema(StepLabelsForm::schema(
                                prefix: 'introduction',
                                buttons: [
                                    TextInput::make("introduction_view_next_button_label")
                                        ->label("Next Button Label")
                                        ->translateLabel()
                                        ->placeholder(__("default_introduction_view_next_button_label"))
                                ]
                            )),

                        Tabs\Tab::make('Bucket')
                            ->badge(2)
                            ->schema(StepLabelsForm::schema(prefix: 'bucket')),

                        Tabs\Tab::make('Generation')
                            ->badge(3)
                            ->schema(StepLabelsForm::schema(prefix: 'extraction')),

                        Tabs\Tab::make('Results')
                            ->badge(4)
                            ->schema(StepLabelsForm::schema(prefix: 'results', buttons: [
                                Toggle::make("allow_download")
                                    ->label("Allow downloading as JSON, CSV, or Excel")
                                    ->translateLabel()
                                    ->onIcon('heroicon-o-check')
                                    ->onColor('success')
                                    ->offIcon('heroicon-o-x-mark')
                                    ->offColor('danger')
                                    ->default(true),

                                TextInput::make("redirect_url")
                                    ->label("Redirect URL")
                                    ->translateLabel()
                                    ->placeholder(__("e.g. https://example.com/redirect")),
                            ])),

                        Tabs\Tab::make('Webhook')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->iconPosition('after')
                            ->schema([
                                Toggle::make("enable_webhook")
                                    ->live()
                                    ->label("Enable Webhook")
                                    ->translateLabel()
                                    ->onIcon('heroicon-o-bell')
                                    ->onColor('success')
                                    ->offIcon('heroicon-o-bell-slash')
                                    ->offColor('danger'),

                                Group::make([
                                    TextInput::make("webhook_url")
                                        ->required(fn (Get $get) => $get('enable_webhook'))
                                        ->hidden(fn (Get $get) => ! $get('enable_webhook'))
                                        ->label("Webhook URL")
                                        ->translateLabel()
                                        ->placeholder(__("e.g. https://example.com/webhook")),

                                    TextInput::make("webhook_secret")
                                        ->hidden(fn (Get $get) => ! $get('enable_webhook'))
                                        ->label("Webhook Bearer Token")
                                        ->translateLabel()
                                        ->helperText(__('A POST request with the Authorization header set to "Bearer <token>" will be sent.'))
                                        ->placeholder(__("e.g. https://example.com/webhook")),
                                ])
                            ]),
                ]),
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
