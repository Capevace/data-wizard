<?php

namespace App\Filament\Resources\SavedExtractorResource\Pages;

use App\Filament\Forms\JsonEditor;
use App\Filament\Resources\SavedExtractorResource;
use App\Filament\Resources\SavedExtractorResource\Actions\GenerateSchemaAction;
use App\Magic\Prompts\GenerateSchemaPrompt;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Mateffy\Magic;
use Mateffy\Magic\Exceptions\UnknownInferenceException;

class CreationWizard extends CreateRecord
{
    protected static string $resource = SavedExtractorResource::class;

    public ?string $generateWith = null;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function generateSchema()
    {
        $schema = $this->generate($this->data['label'], null);

        $json = JsonEditor::formatJson($schema);

        data_set($this, 'data.json_schema', $json);
        $this->dispatch('changed-json-state', statePath: 'json_schema', json: $json);
    }

    protected function generate(string $instructions, ?string $previouslyGeneratedSchema): ?array
    {
        $prompt = new GenerateSchemaPrompt(instructions: $instructions, previouslyGeneratedSchema: $previouslyGeneratedSchema);

        $messages = Magic::chat()
            ->model('cheap')
            ->system($prompt->system())
            ->prompt($prompt)
            ->tools([
                /**
                 * @type $schema {"type": "object", "description": "The JSON Schema you generated", "additionalProperties": true}
                 */
                'generateSchema' => fn (array $schema) => Magic::end(['schema' => $schema]),
            ])
            ->toolChoice('generateSchema')
            ->stream();

        $data = $messages->lastData();

        if ($data === null || ($data['schema'] ?? null) === null) {
            report(new \Exception('Could not generate schema: '.json_encode($messages)));

            Notification::make()
                ->danger()
                ->title('Could not generate schema')
                ->body(json_encode($data))
                ->send();

            return null;
        }

        return $data['schema'];
    }

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->columns(1)
            ->schema([
                Wizard::make([
                    Wizard\Step::make('What are you extracting?')
                        ->schema([
                            TextInput::make('label')
                                ->label('What are you extracting?')
                                ->placeholder('e.g. `Product data from competitor brochures` or `Financial KPIs from q4 reports`'),

                            Placeholder::make('examples')
                                ->label('Presets')
                                ->helperText('Choose a preset if you\'re just trying it out.'),

                            Actions::make([
                                Actions\Action::make('useFinancialPreset')
                                    ->label('KPIs from financial reports')
                                    ->color('gray')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->size('xs')
                                    ->outlined()
                                    ->action(fn (Set $set) => $set('label', 'Financial KPIs from quarterly reports. Total sales, net profit and payroll costs in USD.')),
                                Actions\Action::make('useProductPreset')
                                    ->label('Product data from brochures')
                                    ->color('gray')
                                    ->icon('heroicon-o-shopping-cart')
                                    ->size('xs')
                                    ->outlined()
                                    ->action(fn (Set $set) => $set('label', 'Product data from competitor brochures. Product names, normal prices and discount prices. Associate image if possible.')),
                                Actions\Action::make('useRealEstatePreset')
                                    ->label('Real estate data from listings and exposés')
                                    ->color('gray')
                                    ->icon('heroicon-o-shopping-cart')
                                    ->size('xs')
                                    ->outlined()
                                    ->action(fn (Set $set) => $set('label', 'Real estate property information from listings and exposés. Property address, price, size and number of rooms. Associate images if possible.')),
                            ]),
                        ]),

                    Wizard\Step::make('Data format')
                        ->schema([
                            JsonEditor::make('json_schema')
                                ->id('data.json_schema')
                                ->required()
                                ->label('JSON Schema')
                                ->translateLabel()
                                ->hintActions([
                                    GenerateSchemaAction::make('generateSchema'),
                                ]),
                        ])
                ])
                    ->registerListeners([
                        'wizard::nextStep' => [
                            function (Wizard $wizard, string $statePath, int $currentStepIndex) {
                                if ($currentStepIndex === 0 && $this->generateWith === null) {
                                    try {
                                        $this->generateSchema();
                                    } catch (UnknownInferenceException $e) {
                                        report($e);

                                        Notification::make()
                                            ->danger()
                                            ->title($e->getTitle())
                                            ->body($e->getMessage())
                                            ->send();

                                        return;
                                    }
                                }
                            }
                        ]
                    ])
            ]);
    }
}
