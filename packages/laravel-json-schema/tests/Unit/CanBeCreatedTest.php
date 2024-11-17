<?php

use Mateffy\JsonSchema;
use Mateffy\JsonSchema\Exceptions\InvalidType;


it('can be created with array', function () {
    $schema = new Mateffy\JsonSchema([
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
    ]);

    expect($schema->toArray())->toBeArray();
});

it('throws without type', function () {
    new Mateffy\JsonSchema([]);
})->throws(InvalidType::class);

it('throws with invalid type', function ($type) {
    new Mateffy\JsonSchema(['type' => $type]);
})
    ->throws(InvalidType::class)
    ->with([
        ['invalid'],
        [false],
        ['foo'],
        ['false'],
        [0],
        [1],
        [null],
        [[]],
    ]);

it('requires type', function ($type) {
    $schema = new Mateffy\JsonSchema(['type' => $type]);

    expect($schema->satisfies($type))->toBeTrue();
    expect($schema->toArray())->toEqual(['type' => $type]);
})
    ->with([
        'string',
        'number',
        'integer',
        'boolean',
        'array',
        'object',
        ['string', 'number'],
        ['string', 'number', 'integer'],
        ['string', 'null'],
        ['string', 'number', 'null'],
    ]);


//it('can be created with helper', function ($json) {
//    $schema = Mateffy\JsonSchema::from($json);
//
//    expect($schema->toArray())->toBeArray();
//    expect($schema->satisfies($json['type']))->toBeTrue();
//    expect($schema->toArray())->toEqual($json);
//})
//    ->with();

it('can be created with helper with nullable', function ($json) {
    $schema = Mateffy\JsonSchema::from($json, nullable: true);

    if ($json['type'] === 'boolean') {
        dd($json, $schema->toArray());
    }

    expect($schema->toArray())->toBeArray()
        ->and($schema->satisfies('null'))->toBeTrue()
        ->and($schema->toArray())->toEqual([
            ...$json,
            'type' => JsonSchema::normalizeType($json['type'], ['null']),
        ]);
})
    ->with('schemas');

it('tests', function () {
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
})->only();
