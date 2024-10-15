<?php

namespace Mateffy\Magic\Memory;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mateffy\Magic\Memory\Interfaces\MemoryCollectorInterface;
use Mateffy\Magic\Memory\Interfaces\MemoryInterface;
use Phpml\Math\Distance\Euclidean;
use function Codewithkyrian\Transformers\Pipelines\pipeline;

class SimpleMemoryCollector implements MemoryCollectorInterface, Arrayable
{
    public function __construct(
        /**
         * @var Collection<MemoryInterface>
         */
        protected Collection $memories,
    )
    {
    }

    public function all(): Collection
    {
        return $this->memories;
    }

    public function search(string $search): Collection
    {
        $searchEmbedding = $this->computeEmbedding($search);

        $ids = $this->memories
            ->filter(fn (SimpleMemory $memory) => $memory->getEmbedding())
            ->mapWithKeys(fn (SimpleMemory $memory) => [
                $memory->getId() => $this->computeDistance($memory->getEmbedding(), $searchEmbedding)
            ])
            ->filter(fn (float $distance) => $distance < 15)
            ->sortByDesc(fn (float $distance) => $distance)
            ->keys()
            ->take(10)
            ->all();

        return $this->getMany($ids);
    }

    protected function computeEmbedding(string $content): array
    {
        $vector = pipeline('embeddings', 'TaylorAI/gte-tiny');

        return $vector($content)[0][0];
    }

    protected function computeDistance(array $a, array $b): float
    {
        $euclidean = new Euclidean;
        $distance = $euclidean->distance($a, $b);

        return $distance;
    }

    public function getMany(array $ids): Collection
    {
        return collect($ids)
            ->map(fn (string $id) => $this->get($id))
            ->filter()
            ->values();
    }

    public function get(string $id): MemoryInterface
    {
        return $this->memories->get($id);
    }

    public function add(string $memory): string
    {
        $id = Str::uuid()->toString();

        $embedding = $this->computeEmbedding($memory);
        $this->memories->put($id, new SimpleMemory($id, $memory, $embedding));

        return $id;
    }

    public function remove(string $id): void
    {
        $this->memories->forget($id);
    }

    public function toArray(): array
    {
        return $this->memories->toArray();
    }
}
