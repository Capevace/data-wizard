<?php

namespace Mateffy\JsonSchema\Concerns;

trait CanBeWired
{
    public static function fromLivewire($value): static
    {
        return static::from($value);
    }

    public function toLivewire(): array
    {
        return $this->toArray();
    }
}
