<?php

namespace Capevace\MagicImport\Builder\Concerns;

trait HasSchema
{
    public array $schema = [];

    public function schema(array $schema): static
    {
        $this->schema = $schema;

        return $this;
    }
}
