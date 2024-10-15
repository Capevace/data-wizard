<?php

namespace Mateffy\Magic\Memory;

use Illuminate\Support\Collection;
use JsonException;

class MagicMemory
{
    /**
     * @param ?Collection<SimpleMemory> $memories
     * @throws JsonException
     */
    public function simple(?Collection $memories = null): SimpleMemoryCollector
    {
        return new SimpleMemoryCollector($memories ?? collect());
    }

    /**
     * @throws JsonException
     */
    public function file(string $path): FlatFileMemoryCollector
    {
        return FlatFileMemoryCollector::load($path);
    }
}
