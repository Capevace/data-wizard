<?php

namespace Mateffy\Magic\LLM\Message;

readonly class FunctionCall
{
    public function __construct(
        public string $name,
        public array $arguments
    ) {}

    public static function tryFrom(?array $data): ?static
    {
        if (! $data || ! isset($data['name']) || ! isset($data['arguments'])) {
            return null;
        }

        return new static(
            name: $data['name'],
            arguments: $data['arguments'] ?? [],
        );
    }
}
