<?php

namespace Mateffy\Magic\Functions;

class ModifyData implements InvokableFunction
{
    public function __construct(
        protected array $schema,
    ) {}

    public function name(): string
    {
        return 'modifyData';
    }

    public function description(): ?string
    {
        return 'Modify the data.';
    }

    public function schema(): array
    {
        return $this->schema;
    }

    public function validate(array $arguments): array
    {
        return $arguments;
    }

    public function execute(array $arguments): mixed
    {
        return null;
    }

    public function callback(): \Closure
    {
        return fn () => null;
    }
}
