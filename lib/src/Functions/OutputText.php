<?php

namespace Capevace\MagicImport\Functions;

use Closure;

class OutputText implements InvokableFunction
{
    public function name(): string
    {
        return 'outputText';
    }

    public function schema(): array
    {
        return [
            'name' => 'outputText',
            'description' => 'Output some text. You can use markdown to format it.',
            'arguments' => [
                'text' => [
                    'type' => 'string',
                    'description' => 'Text to output'
                ]
            ],
            'required' => ['text'],
        ];
    }

    public function validate(array $data): array
    {
        $validator = validator($data, [
            'text' => 'required|string',
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