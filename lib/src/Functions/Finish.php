<?php

namespace Mateffy\Magic\Functions;

use Closure;

class Finish implements InvokableFunction
{
    public function name(): string
    {
        return 'finish';
    }

    public function schema(): array
    {
        return [
            'name' => 'finish',
            'description' => 'Output some text as a result. Will be formatted as such.',
            'arguments' => [
                'text' => [
                    'type' => 'string',
                    'description' => 'Final message to output',
                ],
            ],
            'required' => ['text'],
        ];
    }

    public function validate(array $data): array
    {
        $validator = validator($data, [
            'text' => 'nullable|string',
        ]);

        $validator->validate();

        return $validator->validated();
    }

    public function execute(array $data): mixed
    {
        return null;
    }

    public function callback(): Closure
    {
        return fn () => null;
    }
}
