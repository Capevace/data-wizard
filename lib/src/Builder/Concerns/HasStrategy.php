<?php

namespace Capevace\MagicImport\Builder\Concerns;

trait HasStrategy
{
    public ?string $strategy = null;

    public function strategy(?string $strategy): static
    {
        $this->strategy = $strategy;

        return $this;
    }
}
