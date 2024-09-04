<?php

namespace App\Filament\Forms;

use Dotswan\FilamentCodeEditor\Fields\CodeEditor;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Swaggest\JsonSchema\JsonSchema;
use Swaggest\JsonSchema\Schema;

class JsonEditor extends CodeEditor
{
    protected string $view = 'filament.fields.code-editor';

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->json()
            ->live()
            ->darkModeTheme('nord')
            ->lightModeTheme('basic-light')
            ->hintActions([
                Action::make('validate')
                    ->icon('heroicon-o-check-badge')
                    ->label('Validate Schema')
                    ->translateLabel()
                    ->color('success')
                    ->action(function (Get $get, Set $set) {
                        $json = $this->getState();

                        if (!$json) {
                            return;
                        }

                        try {
                            $parsed = json_decode($json, associative: false, flags: JSON_THROW_ON_ERROR);
                        } catch (\JsonException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('Invalid JSON'))
                                ->body($e->getMessage())
                                ->send();

                            return;
                        }

                        try {
                            $validated = JsonSchema::import($parsed);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('Invalid JSON Schema'))
                                ->body($e->getMessage())
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->success()
                            ->title(__('JSON Schema is valid'))
                            ->send();
                    })
            ])
            ->dehydrateStateUsing(fn (?string $state) => $state ? json_decode($state, true) : null)
            ->formatStateUsing(fn (array|string|null $state) => $state && !is_string($state)
                // Replace 4 spaces with 2 spaces for more compact editing
                ? JsonEditor::formatJson($state)
                : null
            )
            ->default(<<<JSON
            {
              "type": "array",
              "items": {
                "type": "object",
                "properties": {
                  "name": {
                    "type": "string",
                    "description": "The name of the product"
                  },
                  "price": {
                    "type": "number",
                    "description": "The price in EUR"
                  }
                },
                "required": ["name"],
                "additionalProperties": false
              }
            }
            JSON);
    }

    public static function formatJson(array $json): string
    {
        return preg_replace_callback(
            pattern: '/^(?: {4})+/m',
            callback: fn($m) => str_repeat(' ', 2 * (strlen($m[0]) / 4)),
            subject: json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
        );
    }
}
