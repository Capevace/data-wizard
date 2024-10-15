<?php

namespace Mateffy\Magic\LLM\Message;

use Illuminate\Support\Str;
use Mateffy\Magic\Prompt\Role;
use Mateffy\Magic\Utils\PartialJson;

class FunctionInvocationMessage implements DataMessage, PartialMessage
{
    use WireableViaArray;

    public function __construct(
        public Role $role,
        public ?FunctionCall $call = null,
        public ?string $partial = null,
        public ?array $schema = null,
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'call' => $this->call->toArray(),
            'partial' => $this->partial,
            'schema' => $this->schema,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static(
            role: Role::from($data['role']),
            call: FunctionCall::fromArray($data['call']),
            partial: $data['partial'] ?? null,
            schema: $data['schema'] ?? null,
        );
    }

    public function data(): ?array
    {
        return $this->call->arguments;
    }

    public function text(): ?string
    {
        return $this->call->name;
    }

    public function append(string $chunk): static
    {
        $this->partial .= $chunk;

        $data = PartialJson::parse($this->partial);
        $this->call = new FunctionCall(
            name: $this->call->name,
            arguments: $data ?? $this->call->arguments,
            id: $this->call->id,
        );

        return $this;
    }

    public function appendFull(string $chunk): static
    {
        $this->partial .= $chunk;

        $data = PartialJson::parse($this->partial);
        $this->call = ($data['name'] ?? $this->call?->name)
            ? new FunctionCall(
                name: $data['name'] ?? $this->call?->name,
                arguments: $data['parameters'] ?? $this->call?->arguments ?? [],
                id: $this->call?->id ?? Str::uuid()->toString(),
            )
            : null;

        return $this;
    }

    public static function fromChunk(string $chunk): static
    {
        $data = PartialJson::parse($chunk);

        if ($call = FunctionCall::tryFrom($data)) {
            return new self(
                role: Role::Assistant,
                call: $call,
                partial: $chunk,
            );
        }

        return new self(
            role: Role::Assistant,
            call: null,
            partial: $chunk,
        );
    }

    public static function call(FunctionCall $call): static
    {
        return new self(
            role: Role::Assistant,
            call: $call,
            partial: null,
        );
    }
}
