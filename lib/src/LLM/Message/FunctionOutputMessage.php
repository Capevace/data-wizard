<?php

namespace Capevace\MagicImport\LLM\Message;

use Capevace\MagicImport\Prompt\Role;

class FunctionOutputMessage implements Message
{
    public function __construct(
        public Role $role,
        public FunctionCall $call,
        public mixed $output
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
        if (! is_string($this->output)) {
            return null;
        }

        return $this->output;
    }
}
