<?php

namespace Mateffy\Magic\Functions;

use Closure;
use Mateffy\Magic\LLM\Message\FunctionCall;

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

    public function validate(array $arguments): array
    {
        $validator = validator($arguments, [
            'text' => 'nullable|string',
        ]);

        $validator->validate();

        return $validator->validated();
    }

    public function execute(FunctionCall $call): mixed
    {
        return null;
    }

    public function callback(): Closure
    {
        return fn () => null;
    }
}
