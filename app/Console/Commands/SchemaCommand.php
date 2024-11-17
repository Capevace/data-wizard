<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use Mateffy\JsonSchema;

class SchemaCommand extends Command
{
    protected $signature = 'schema';

    protected $description = 'Command description';

    public function handle(): void
    {
        $schema = JsonSchema::array([
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'The name of the product',
                ],
                'price' => [
                    'anyOf' => [
                        [
                            'type' => 'object',
                            'properties' => [
                                'amount' => [
                                    'type' => 'number',
                                    'description' => 'The price in EUR',
                                ],
                                'currency' => [
                                    'type' => 'string',
                                    'description' => 'The currency',
                                ],
                            ],
                            'required' => ['amount', 'currency'],
                        ],
                        ['type' => 'number'],
                    ],
                ],
            ],
            'required' => ['name', 'price'],
        ]);

        try {
            $schema->validate([
                [
                    'name' => 'Test',
                    'price' => [
                        'amount' => 100,
                        'currency' => 'EUR',
                    ]
                ],
                [
                    'name' => 'Test',
                    'price' => '222',
                ]
            ]);
        } catch (ValidationException $e) {
            $this->info(json_encode($e->errors(), JSON_PRETTY_PRINT));
        }
    }
}
