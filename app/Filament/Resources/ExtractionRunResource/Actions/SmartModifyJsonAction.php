<?php

namespace App\Filament\Resources\ExtractionRunResource\Actions;

use App\Filament\Forms\JsonEditor;
use Capevace\MagicImport\LLM\ElElEm;
use Capevace\MagicImport\LLM\Models\Claude3Family;
use Capevace\MagicImport\Loop\Response\JsonResponse;
use Capevace\MagicImport\Prompt\SmartModifyPrompt;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Livewire\Component;

class SmartModifyJsonAction extends Action
{
    protected Closure|array $jsonSchema = [];

    protected Closure|array $dataToModify = [];

    protected Closure|string $modificationInstructions = '';

    public function jsonSchema(Closure|array $jsonSchema): static
    {
        $this->jsonSchema = $jsonSchema;

        return $this;
    }

    public function dataToModify(Closure|array $dataToModify): static
    {
        $this->dataToModify = $dataToModify;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Smart Modify JSON')
            ->translateLabel()
            ->icon('bi-magic')
            ->fillForm(fn () => [
                'json' => $this->evaluate($this->dataToModify),
                'modification_instructions' => $this->modificationInstructions,
            ])
            ->form([
                JsonEditor::make('json'),
                Textarea::make('modification_instructions'),
                Actions::make([])
                    ->actions([
                        Actions\Action::make('modify')
                            ->label('Modify')
                            ->translateLabel()
                            ->icon('bi-magic')
                            ->action(function (Get $get, Set $set, Component $livewire) {
                                $json = $get('json');
                                $instructions = $get('modification_instructions');

                                if (empty($instructions) || empty($json)) {
                                    return;
                                }

                                $modifiedJson = $this->generate(data: $json, instructions: $instructions);
                                $set('json', $modifiedJson);

                                // Update/re-render the JsonEditor component
                                $livewire->dispatch('changed-json-state', statePath: 'json', json: $modifiedJson);
                            }),
                    ]),

            ])
            ->action(fn () => null);
    }

    protected function generate(string $data, string $instructions): ?string
    {
        $prompt = new SmartModifyPrompt(
            dataToModify: $data,
            schema: $this->evaluate($this->jsonSchema),
            modificationInstructions: $instructions,
        );

        $llm = ElElEm::fromString(ElElEm::id('anthropic', Claude3Family::HAIKU));

        $responses = $llm->stream(prompt: $prompt);

        $response = collect($responses)->first(fn ($message) => $message instanceof JsonResponse);

        if ($response === null) {
            report(new \Exception('Could not extract data: '.json_encode($responses)));

            Notification::make()
                ->danger()
                ->title('Could not extract data')
                ->send();

            return null;
        }

        return ($properties = $response->data ?? null)
            ? JsonEditor::formatJson($properties)
            : null;
    }
}
