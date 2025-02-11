<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Filament\Forms\JsonEditor;
use App\Magic\Prompts\GenerateSchemaPrompt;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Livewire\Component;
use Mateffy\Magic;

class GenerateSchemaAction extends Actions\Action
{
    protected string $schemaStatePath = 'data.json_schema';

    public function schemaStatePath(string $schemaStatePath): static
    {
        $this->schemaStatePath = $schemaStatePath;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Generate a new JSON schema')
            ->translateLabel()
            ->icon('bi-magic')
            ->modalDescription(__('If you don\'t know how to write a JSON schema yourself, you can use this tool to generate one for you.'))
            ->modalIcon('bi-magic')
            ->modalSubmitActionLabel(__('Use'))
            ->modalFooterActionsAlignment('end')
            ->fillForm(fn (Component $livewire) => [
                'schema_instructions' => '',
                'edit_schema' => json_decode(data_get($livewire, $this->schemaStatePath), associative: true),
            ])
            ->form([
                Textarea::make('schema_instructions')
                    ->required()
                    ->label('What should the schema contain?')
                    ->translateLabel()
                    ->placeholder(__('e.g. Products with thumbnail images')),

                JsonEditor::make('edit_schema')
                    ->hintAction(
                        Actions\Action::make('clear')
                            ->label('Clear')
                            ->translateLabel()
                            ->icon('bi-trash')
                            ->color('gray')
                            ->action(function (Set $set, array $data, Component $livewire) {
                                $set($this->getComponent()->getStatePath(), null);

                                $livewire->dispatch('changed-json-state', statePath: 'edit_schema', json: null);
                            })
                    ),

                Actions::make([])
                    ->fullWidth()
                    ->actions([
                        Actions\Action::make('generate')
                            ->label('Generate')
                            ->translateLabel()
                            ->icon('bi-magic')
                            ->action(function (Get $get, Set $set, Component $livewire) {
                                $schema = $get('edit_schema');
                                $instructions = $get('schema_instructions');

                                if (empty($instructions)) {
                                    Notification::make()
                                        ->danger()
                                        ->title(__('Missing instructions'))
                                        ->send();

                                    return;
                                }

                                $schema = $this->generate(instructions: $instructions, previouslyGeneratedSchema: $schema);
                                $set('edit_schema', $schema);

                                // Update/re-render the JsonEditor component
                                $livewire->dispatch('changed-json-state', statePath: 'edit_schema', json: $schema);
                            }),
                    ]),

            ])
            ->action(function (Set $set, array $data, Component $livewire) {
                $set($this->getComponent()->getStatePath(), JsonEditor::formatJson($data['edit_schema']));

                data_set($livewire, 'json_schema', JsonEditor::formatJson($data['edit_schema']));
                $livewire->dispatch('changed-json-state', statePath: 'json_schema', json: JsonEditor::formatJson($data['edit_schema']));
            });
    }

    protected function generate(string $instructions, ?string $previouslyGeneratedSchema): ?string
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
//            ->onMessageProgress(fn (Message $message) => dd($message))
//            ->onMessage(fn (Message $message) => dd($message))
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

        return JsonEditor::formatJson($data['schema']);
    }
}
