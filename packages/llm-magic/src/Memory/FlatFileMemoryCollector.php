<?php

namespace Mateffy\Magic\Memory;

use Illuminate\Support\Collection;
use JsonException;
use Mateffy\Magic\Memory\Interfaces\MemoryInterface;

class FlatFileMemoryCollector extends SimpleMemoryCollector
{
    public function __construct(
        /**
         * @var Collection<MemoryInterface>
         */
        protected Collection $memories,

        protected string $path,
    )
    {
        parent::__construct($memories);
    }

    public function add(string $memory): string
    {
        $id = parent::add($memory);

        $this->save();

        return $id;
    }

    public function remove(string $id): void
    {
        parent::remove($id);

        $this->save();
    }

    /**
     * @throws JsonException
     */
    public function save(): void
    {
        file_put_contents($this->path, json_encode($this->toArray(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }

    /**
     * @throws JsonException
     */
    public function reload(): void
    {
        $this->memories = self::loadMemories($this->path);
    }

    /**
     * @throws JsonException
     */
    protected static function loadMemories(string $path): Collection
    {
        $array = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        return collect($array)
            ->map(fn (array $data) => SimpleMemory::fromArray($data));
    }

    /**
     * @throws JsonException
     */
    public static function load(string $path): self
    {
        return new self(
            memories: self::loadMemories($path),
            path: $path,
        );
    }
}
