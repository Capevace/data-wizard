<?php

namespace Capevace\MagicImport\Functions;

use Swaggest\JsonSchema\SchemaContract;

class Extract implements InvokableFunction
{
    public function __construct(
        protected SchemaContract $schema,
    )
    {
    }

    public function name(): string
    {
        return 'extract';
    }

    public function schema(): array
    {
        return json_decode(json_encode($this->schema), associative: true);
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