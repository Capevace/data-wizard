<?php

namespace Mateffy\Magic\Memory\Interfaces;

use Illuminate\Support\Collection;

interface MemoryCollectorInterface
{
    /**
     * @return Collection<MemoryInterface>
     */
    public function all(): Collection;

    /**
     * @return Collection<MemoryInterface>
     */
    public function search(string $search): Collection;

    /**
     * @return Collection<MemoryInterface>
     */
    public function getMany(array $ids): Collection;

    public function get(string $id): MemoryInterface;
    public function add(string $memory): string;
    public function remove(string $id): void;
}
