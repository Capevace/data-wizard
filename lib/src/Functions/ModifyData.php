<?php

namespace Capevace\MagicImport\Functions;

use Swaggest\JsonSchema\SchemaContract;

class ModifyData implements InvokableFunction
{
    public function __construct(
        protected array $schema,
    )
    {
    }

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
