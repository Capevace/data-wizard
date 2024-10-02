<?php

namespace Mateffy\Magic\LLM\Message;

use Mateffy\Magic\Prompt\Role;
use Mateffy\Magic\Utils\PartialJson;

class FunctionInvocationMessage implements DataMessage, PartialMessage
{
    public function __construct(
        public Role $role,
        public ?FunctionCall $call = null,
        public ?string $partial = null,
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'function_call' => [
                'name' => $this->call->name,
                'arguments' => json_encode($this->call->arguments, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ],
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static(
            role: Role::tryFrom($data['role']) ?? Role::Assistant,
            call: new FunctionCall(
                name: $data['function_call']['name'],
                arguments: json_decode($data['function_call']['arguments'], true, 512, JSON_THROW_ON_ERROR)
            )
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
        );

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

}
