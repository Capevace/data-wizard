<?php

namespace Mateffy\Magic\Functions;

use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\Magic;

class MergeData implements InvokableFunction
{
    public function __construct(
        protected array $schema,
    ) {}

    public function name(): string
    {
        return 'mergeData';
    }

    public function description(): ?string
    {
        return 'Merge the data together by calling this function. The data you pass in should be the data that you merged together. This will save it.';
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
