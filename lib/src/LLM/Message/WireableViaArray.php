<?php

namespace Mateffy\Magic\LLM\Message;

trait WireableViaArray
{
    public static function fromLivewire($value): static
    {
        return self::fromArray($value);
    }

    public function toLivewire(): array
    {
        return $this->toArray();
    }
}
