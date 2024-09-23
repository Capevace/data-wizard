<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Filament\Forms\JsonEditor;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Loop\Response\JsonResponse;
use Mateffy\Magic\Prompt\GenerateSchemaPrompt;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Livewire\Component;

class GenerateSchemaAction extends Actions\Action
{
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
                'edit_schema' => json_decode(data_get($livewire, 'data.json_schema'), associative: true),
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

                $livewire->dispatch('changed-json-state', statePath: 'json_schema', json: JsonEditor::formatJson($data['edit_schema']));
            });
    }

    protected function generate(string $instructions, ?string $previouslyGeneratedSchema): ?string
    {
        $prompt = new GenerateSchemaPrompt(instructions: $instructions, previouslyGeneratedSchema: $previouslyGeneratedSchema);

        $llm = ElElEm::fromString(ElElEm::id('anthropic', Claude3Family::HAIKU));

        $responses = $llm->stream(prompt: $prompt);

        $response = collect($responses)->first(fn ($message) => $message instanceof JsonResponse);

        if ($response === null) {
            report(new \Exception('Could not generate schema: '.json_encode($responses)));

            Notification::make()
                ->danger()
                ->title('Could not generate schema')
                ->send();

            return null;
        }

        return ($properties = $response->data['schema'] ?? null)
            ? JsonEditor::formatJson($properties)
            : null;
    }
}
