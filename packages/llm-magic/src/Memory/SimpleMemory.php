<?php

namespace Mateffy\Magic\Memory;

use Illuminate\Contracts\Support\Arrayable;
use Mateffy\Magic\Memory\Interfaces\EmbeddingMemoryInterface;
use Mateffy\Magic\Memory\Interfaces\MemoryInterface;
use Rubix\ML\Kernels\Distance\Euclidean;

class SimpleMemory implements MemoryInterface, EmbeddingMemoryInterface, Arrayable
{
    public function __construct(
        public string $id,
        public string $text,
        public ?array $embedding = null,
    )
    {
    }

    public function computeDistance(array $search): float
    {
        return (new Euclidean)->compute($this->embedding, $search);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public static function fromArray(array $data): static
    {
        return new static(
            id: $data['id'],
            text: $data['text'],
            embedding: $data['embedding'] ?? null,
        );
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'embedding' => $this->embedding,
        ];
    }

    public function getEmbedding(): array
    {
        return $this->embedding ?? [];
    }
}
