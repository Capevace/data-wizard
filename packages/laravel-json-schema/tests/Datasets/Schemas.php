<?php
namespace Tests\Datasets;



dataset('schemas', [
    [[
        'type' => 'object',
        'properties' => [
            'name' => [
                'type' => 'string',
                'description' => 'The name of the product',
            ],
            'price' => [
                'type' => 'number',
                'description' => 'The price in EUR',
            ],
        ],
        'required' => ['name'],
    ]],
    [[
        'type' => 'array',
        'items' => [
            'type' => 'object',
            'properties' => [
                'name' => [
                    'type' => 'string',
                    'description' => 'The name of the product',
                ],
                'price' => [
                    'type' => 'number',
                    'description' => 'The price in EUR',
                ],
            ],
            'required' => ['name'],
        ],
    ]],
    [[
        'type' => 'string',
    ]],
    [[
        'type' => 'number',
    ]],
    [[
        'type' => 'integer',
    ]],
    [[
        'type' => 'boolean',
    ]],
    [[
        'type' => 'null',
    ]]
]);
