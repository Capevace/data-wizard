<?php

namespace App\Filament\Resources;

use App\Filament\Forms\JsonEditor;
use App\Filament\Forms\ModelSelect;
use App\Filament\Forms\StrategySelect;
use App\Filament\Resources\SavedExtractorResource\Pages;
use App\Filament\Resources\SavedExtractorResource\Actions\BulkExportExtractorAction;
use App\Filament\Resources\SavedExtractorResource\Actions\GenerateSchemaAction;
use App\Filament\Resources\SavedExtractorResource\Actions\OpenEmbeddedExtractorAction;
use App\Filament\Resources\SavedExtractorResource\RelationManagers\RunsRelationManager;
use App\Filament\Resources\SavedExtractorResource\StepLabelsForm;
use App\Models\SavedExtractor;
use App\Models\SavedExtractor\RedirectMode;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
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
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic;

class SavedExtractorResource extends Resource
{
    public const MIN_CHUNK_SIZE = 1000;

    protected static ?string $model = SavedExtractor::class;

    protected static ?string $slug = 'saved-extractors';

    protected static ?string $navigationIcon = 'bi-robot';

    public static function getModelLabel(): string
    {
        return __('Extractor');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Extractors');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->live(onBlur: true)
            ->schema([
                Tabs::make('UI')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Tabs\Tab::make('Data Schema & Strategy')
                            ->icon('heroicon-o-code-bracket')
                            ->iconPosition('after')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextInput::make('label')
                                            ->required()
                                            ->label('Label')
                                            ->translateLabel()
                                            ->placeholder(__('e.g. Extracting products from brochure PDFs')),

                                        StrategySelect::make('strategy')
                                            ->required()
                                            ->live(),

                                        ModelSelect::make('model')
                                            ->required()
                                            ->live()
                                    ]),

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
                                    ->helperText(__('You can write prompts in other languages, but English is recommended as the rest of the process is also in English. However, if you want the LLM to output a specific language, you can simply tell it here.'))
                            ]),

                        Tabs\Tab::make('Context Options')
                            ->icon('heroicon-o-cube')
                            ->iconPosition('after')
                            ->schema([
                                Section::make('Chunk Size')
                                    ->description('You can configure how large the batches of data should be that are sent to the LLM for processing.')
                                    ->icon('heroicon-o-cube-transparent')
                                    ->aside()
                                    ->schema([
                                        TextInput::make('chunk_size')
                                            ->label('Chunk size (in tokens)')
                                            ->translateLabel()
                                            ->placeholder(config('llm-magic.artifacts.default_max_tokens'))
                                            ->suffix(__('tokens'))
                                            ->numeric()
                                            ->step(1)
                                            ->minValue(self::MIN_CHUNK_SIZE)
                                    ]),

                                Section::make('Text')
                                    ->description('The text of uploaded documents is extracted, split by pages and included in the prompt.')
                                    ->icon('bi-fonts')
                                    ->aside()
                                    ->schema([
                                        Toggle::make('include_text')
                                            ->label('Include text')
                                            ->translateLabel()
                                            ->onIcon('bi-fonts')
                                            ->onColor('success')
                                            ->offIcon('bi-x')
                                            ->offColor('danger')
                                            ->default(true),
                                    ]),

                                Section::make('Images')
                                    ->description('Any images you upload or that are contained within documents can be sent to the LLM to be processed with Vision capabilities.')
                                    ->icon('bi-image')
                                    ->aside()
                                    ->schema([
                                        Toggle::make('include_embedded_images')
                                            ->label('Include images')
                                            ->translateLabel()
                                            ->onIcon('bi-image')
                                            ->onColor('success')
                                            ->offIcon('bi-x')
                                            ->offColor('danger')
                                            ->default(true)
                                            ->helperText(__('Only possible if the LLM has Vision capabilities.')),

                                        Toggle::make('mark_embedded_images')
                                            ->label('Mark images with identifiers')
                                            ->translateLabel()
                                            ->hidden(fn (Get $get) => ! $get('include_embedded_images'))
                                            ->onIcon('bi-123')
                                            ->onColor('success')
                                            ->offIcon('bi-x')
                                            ->offColor('danger')
                                            ->default(true)
                                            ->helperText(__('Enabling this option will draw identifiers onto the images, which are also referenced in the text contents. This helps the LLM associate the images with the text blocks and is especially useful if you want image assignments as part of your data schema.')),
                                    ]),

                                Section::make('PDF Screenshots')
                                    ->description('If you upload PDFs, screenshots of the pages can be sent to the LLM to be processed with Vision capabilities.')
                                    ->icon('bi-file-earmark-pdf')
                                    ->aside()
                                    ->schema([
                                        Toggle::make('include_page_images')
                                            ->label('Include PDF screenshots')
                                            ->translateLabel()
                                            ->onIcon('bi-image')
                                            ->onColor('success')
                                            ->offIcon('bi-x')
                                            ->offColor('danger')
                                            ->default(true)
                                            ->helperText(__('Only possible if the LLM has Vision capabilities.')),

                                        Toggle::make('mark_page_images')
                                            ->label('Mark embedded images on PDF screenshots')
                                            ->translateLabel()
                                            ->hidden(fn (Get $get) => ! $get('include_page_images'))
                                            ->onIcon('heroicon-o-paint-brush')
                                            ->onColor('success')
                                            ->offIcon('bi-x')
                                            ->offColor('danger')
                                            ->default(false)
                                            ->helperText(__('Enabling this option will draw a rectangle around embedded images and place a label next to them in the PDF screenshots. This can help the LLM associate the images with the correct text blocks.')),


                                        Placeholder::make('context_warning')
                                            ->hiddenLabel()
                                            ->extraAttributes(['class' => 'text-warning-600 dark:text-warning-400'])
                                            ->content(__('Enabling both "Include Images" "Include PDF screenshots" can massively increase the number of Vision tokens used and will make the extraction process slower and more expensive.'))
                                            ->hidden(fn (Get $get) => ! $get('include_page_images') || ! $get('include_embedded_images'))
                                    ]),
                            ]),

                        Tabs\Tab::make('Sendoff')
                            ->icon('heroicon-o-paper-airplane')
                            ->iconPosition('after')
                            ->schema([
                                Section::make('Redirect')
                                    ->description(new HtmlString(__("After finishing the data review, the user can be redirected to another URL, likely your application.<br/><br/>When downloads are disabled the user will already be redirected right after editing and submitting the data.")))
                                    ->icon('bi-send')
                                    ->aside()
                                    ->schema([
                                        TextInput::make("redirect_url")
                                            ->label("Redirect URL")
                                            ->translateLabel()
                                            ->placeholder(__("e.g. https://example.com/redirect")),

                                        ToggleButtons::make("redirect_mode")
                                            ->label('Redirect mode')
                                            ->translateLabel()
//                                            ->onIcon('bi-window-plus')
//                                            ->onColor('gray')
//                                            ->offIcon('bi-send')
//                                            ->offColor('gray')
                                            ->options(RedirectMode::class)
                                            ->grouped(),
                                    ]),

                                Section::make('Webhook')
                                    ->description(new HtmlString(__("Webhooks will be sent all along the extraction process and can be used to receive the extracted data, apart from the iFrame API.<br/><br/>A globally configured webhook will be executed anyway.")))
                                    ->icon('bi-bell')
                                    ->aside()
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
                                        ]),
                                    ]),

                                Section::make('Download')
                                    ->icon('bi-download')
                                    ->aside()
                                    ->schema([
                                        Toggle::make("allow_download")
                                            ->label("Allow downloading as JSON, CSV, or Excel")
                                            ->translateLabel()
                                            ->onIcon('bi-cloud-download')
                                            ->onColor('success')
                                            ->offIcon('bi-cloud-slash')
                                            ->offColor('danger')
                                            ->default(true),
                                    ]),
                            ]),

                        Tabs\Tab::make('Introduction')
                            ->badge(1)
                            ->badgeColor('gray')
                            ->schema(StepLabelsForm::schema(
                                prefix: 'introduction',
                                buttons: [
                                    TextInput::make("introduction_view_next_button_label")
                                        ->hiddenLabel()
                                        ->label("Next Button Label")
                                        ->translateLabel()
                                        ->placeholder(__("default_introduction_view_next_button_label"))
                                ]
                            )),

                        Tabs\Tab::make('Bucket')
                            ->badge(2)
                            ->badgeColor('gray')
                            ->schema(StepLabelsForm::schema(prefix: 'bucket')),

                        Tabs\Tab::make('Generation')
                            ->badge(3)
                            ->badgeColor('gray')
                            ->schema(StepLabelsForm::schema(prefix: 'extraction')),

                        Tabs\Tab::make('Results')
                            ->badge(4)
                            ->badgeColor('gray')
                            ->schema(StepLabelsForm::schema(prefix: 'results', buttons: [

                            ])),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->default('No label')
                    ->wrap()
                    ->color(fn ($state) => $state === 'No label' ? 'gray' : null),

                TextColumn::make('output_instructions')
                    ->wrap()
                    ->lineClamp(2)
                    ->limit(100),

                TextColumn::make('strategy')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Str::title($state))
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
            'quick-create' => Pages\CreateSavedExtractor::route('/quick-create'),
            'create' => Pages\CreationWizard::route('/create'),
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

    public static function getRelations(): array
    {
        return [
            'runs' => RunsRelationManager::class,
        ];
    }
}
