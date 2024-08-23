<?php

namespace Capevace\MagicImport\Functions;

use Closure;

class Add implements InvokableFunction
{
    public function name(): string
    {
        return 'add';
    }

    public function schema(): array
    {
        return [
            'name' => 'add',
            'description' => 'Add two numbers',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'a' => [
                        'type' => 'number',
                        'description' => 'The first number to add'
                    ],
                    'b' => [
                        'type' => 'number',
                        'description' => 'The second number to add'
                    ],
                ],
                'required' => ['a', 'b'],
            ],
            'returns' => [
                'type' => 'number',
                'description' => 'Sum of the two numbers'
            ],
        ];
    }

    public function validate(array $data): array
    {
        $validator = validator($data, [
            'a' => 'required|numeric',
            'b' => 'required|numeric'
        ]);

        $validator->validate();

        return $validator->validated();
    }

    public function execute(array $data): mixed
    {
        return $this->callback()($data['a'], $data['b']);
    }

    public function callback(): Closure
    {
        return function (int|float $a, int|float $b): int|float {
            return $a + $b;
        };
    }
}