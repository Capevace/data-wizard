<?php

namespace Mateffy\Magic\Functions;

use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\Magic;

class Extract implements InvokableFunction
{
    public function __construct(
        protected array $schema,
    ) {}

    public function name(): string
    {
        return 'extract';
    }

    public function description(): ?string
    {
        return 'Output the extracted data in the defined schema.';
    }

    public function schema(): array
    {
        return $this->schema;
    }

    public function validate(array $arguments): array
    {
        return $arguments;
    }

    public function execute(FunctionCall $call): mixed
    {
        return Magic::end($call->arguments);
    }

    public function callback(): \Closure
    {
        return fn () => null;
    }
}
