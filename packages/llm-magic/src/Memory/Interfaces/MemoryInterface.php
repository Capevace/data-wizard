<?php

namespace Mateffy\Magic\Memory\Interfaces;

interface MemoryInterface
{
    /**
     * The ID of the memory.
     */
    public function getId(): string;

    /**
     * Get the memory as text.
     */
    public function getText(): string;
}
