<?php

namespace Capevace\MagicImport\Functions;

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

    public function validate(array $data): array
    {
        return $data;
    }

    public function execute(array $data): mixed
    {
        return null;
    }

    public function callback(): \Closure
    {
        return fn () => null;
    }
}
