<?php

namespace App\Filament\Resources\SavedExtractorResource\Actions;

use App\Models\SavedExtractor;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Mateffy\Magic\Chat\TokenStats;
use Mateffy\Magic\Models\ModelCost;

class TestWebhookAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->name('testWebhook')
            ->label('Test Webhook')
            ->translateLabel()
            ->icon('heroicon-o-bell')
            ->action(function (SavedExtractor $record) {
                $record->dispatchWebhook(
                    runId: Str::uuid(),
                    data: [
                        'long_text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                        'nested_data' => [
                            'key' => 'value',
                            'another_key' => "another \n value with a \n newline",

                            'more_nested_data' => [
                                'key' => 'value',
                                'another_key' => 'another value',
                            ],
                        ],
                        'number' => 42,
                        'float' => 3.14,
                        'boolean' => true,
                        'null' => null,
                        'array' => ['one', 'two', 'three'],
                    ],
                    model: $record->model,
                    duration_seconds: 42,
                    tokenStats: new TokenStats(
                        tokens: 42,
                        inputTokens: 42,
                        outputTokens: 42,
                        cost: new ModelCost(
                            inputCentsPer1K: 69.42,
                            outputCentsPer1K: 420.69
                        )
                    )
                );
            });
    }
}
