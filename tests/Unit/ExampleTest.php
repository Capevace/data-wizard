<?php

use Mateffy\JsonSchema;

test('that true is true', function () {
    $schema = JsonSchema::array([
        'type' => 'object',
        'properties' => [
            'name' => [
                'type' => 'string',
                'description' => 'The name of the product',
            ],
            'price' => [
                'type' => 'object',
                'description' => 'The price in EUR',
                'additionalProperties' => false,
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
            ],
        ],
        'required' => ['name', 'price'],
    ]);

    $schema->validate([
        [
            'name' => 'Test',
            'price' => 100
        ],
        [
            'name' => 'Test',
            'price' => 100
        ]
    ]);
});
