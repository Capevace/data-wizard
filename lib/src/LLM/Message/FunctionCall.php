<?php

namespace Mateffy\Magic\LLM\Message;

use Illuminate\Contracts\Support\Arrayable;
use Livewire\Wireable;

class FunctionCall implements Arrayable, Wireable
{
    use WireableViaArray;

    public function __construct(
        public string $name,
        public array $arguments,
        public ?string $id = null,
    ) {}

    public static function tryFrom(?array $data): ?static
    {
        if (! $data || ! isset($data['name']) || ! isset($data['arguments'])) {
            return null;
        }

        return self::fromArray($data);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'arguments' => $this->arguments,
            'id' => $this->id,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            arguments: $data['arguments'] ?? [],
            id: $data['id'] ?? null,
        );
    }
}
