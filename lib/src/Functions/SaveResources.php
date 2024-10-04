<?php

namespace Mateffy\Magic\Functions;

use Closure;
use Mateffy\Magic\LLM\Message\FunctionCall;

class SaveResources implements InvokableFunction
{
    public function name(): string
    {
        return 'save';
    }

    public function schema(): array
    {
        return [
            'name' => 'save',
            'description' => 'Save extracted data to the database',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'models' => [
                        'type' => 'array',
                        'description' => 'The models to save',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => [
                                    'type' => 'string',
                                    'description' => 'The name of the model',
                                ],
                                'data' => [
                                    'type' => 'object',
                                    'description' => 'The data to save',
                                ],
                            ],
                            'required' => ['name', 'data'],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function validate(array $arguments): array
    {
        $validator = validator($arguments, [
            'models' => 'required|array',
        ]);

        $validator->validate();

        return $validator->validated();
    }

    public function execute(FunctionCall $call): mixed
    {
        return $this->callback()();
    }

    public function callback(): Closure
    {
        return fn () => null;
    }
}
