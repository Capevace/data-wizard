<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Filament\Resources\SavedExtractorResource\Pages\EditSavedExtractor;
use Filament\Actions\Action;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Mateffy\Magic;
use Mateffy\Magic\Models\Anthropic;

class GenerateStepLabelsAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Generate UI texts')
            ->translateLabel()
            ->icon('bi-magic')
            ->action(function (Set $set, array $data, EditSavedExtractor $livewire) {
                $json_schema = data_get($livewire, 'data.json_schema');
                $instructions = data_get($livewire, 'data.output_instructions');

                $texts = $this->generate(instructions: $instructions, schema: $json_schema);

                $keys = [
                    'introduction_view_heading',
                    'introduction_view_description',
                    'introduction_view_next_button_label',
                    'bucket_view_heading',
                    'bucket_view_description',
                    'bucket_view_back_button_label',
                    'bucket_view_begin_button_label',
                    'bucket_view_continue_button_label',
                    'extraction_view_heading',
                    'extraction_view_description',
                    'extraction_view_back_button_label',
                    'extraction_view_continue_button_label',
                    'extraction_view_restart_button_label',
                    'results_view_heading',
                    'results_view_description',
                    'results_view_back_button_label',
                    'results_view_next_button_label',
                ];

                foreach ($keys as $key) {
                    data_set($livewire, "data.{$key}", data_get($texts, $key));
                }
            });
    }

    protected function generate(?string $instructions, ?string $schema = null): ?array
    {
        $prompt = new \App\Magic\Prompts\GenerateStepLabelsPrompt(instructions: $instructions ?? '', schema: $schema);

        $responses = Magic::chat()
            ->model('cheap')
            ->prompt($prompt)
            ->stream();

        $response = $responses->lastData();

        if ($response === null) {
            report(new \Exception('Could not generate texts: '.json_encode($responses)));

            Notification::make()
                ->danger()
                ->title('Could not generate texts')
                ->send();

            return null;
        }

        return $response;
    }

    public static function getDefaultName(): ?string
    {
        return 'generateUILabels';
    }
}
