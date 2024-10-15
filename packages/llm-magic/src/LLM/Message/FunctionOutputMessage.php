<?php

namespace Mateffy\Magic\LLM\Message;

use Mateffy\Magic\Prompt\Role;

class FunctionOutputMessage implements Message, DataMessage
{
    use WireableViaArray;

    public function __construct(
        public Role $role,
        public FunctionCall $call,
        public mixed $output,
        public bool $endConversation = false,
    ) {}

    public function data(): ?array
    {
        if (is_array($this->output)) {
            return $this->output;
        }

        if ($this->output === null) {
            return null;
        }

        return json_decode($this->output, true);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            role: Role::from($data['role']),
            call: FunctionCall::fromArray($data['call']),
            output: $data['output'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'call' => $this->call->toArray(),
            'output' => $this->output,
        ];
    }

    public function json(): ?array
    {
        if (! is_array($this->output)) {
            return null;
        }

        return $this->output;
    }

    public function text(): ?string
    {
        if ($this->output === null) {
            return null;
        }

        if (! is_string($this->output)) {
            return json_encode($this->output, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $this->output;
    }

    public static function output(FunctionCall $call, mixed $output): static
    {
        return new self(role: Role::User, call: $call, output: $output);
    }

    public static function end(FunctionCall $call, mixed $output): static
    {
        return new self(role: Role::User, call: $call, output: $output, endConversation: true);
    }

    public static function error(FunctionCall $call, string $message): static
    {
        return new self(role: Role::User, call: $call, output: ['error' => $message]);
    }

    public static function canceled(FunctionCall $call): static
    {
        return new self(role: Role::User, call: $call, output: 'canceled');
    }
}
