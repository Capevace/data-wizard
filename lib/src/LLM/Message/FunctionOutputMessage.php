<?php

namespace Mateffy\Magic\LLM\Message;

use Mateffy\Magic\Prompt\Role;

class FunctionOutputMessage implements Message
{
    public function __construct(
        public Role $role,
        public FunctionCall $call,
        public mixed $output,
        public bool $endConversation = false,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            role: Role::tryFrom($data['role']) ?? Role::Assistant,
            call: new FunctionCall(
                name: $data['function_call'],
                arguments: []
            ),
            output: $data['output'],
        );
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'function_call' => $this->call->name,
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
}
