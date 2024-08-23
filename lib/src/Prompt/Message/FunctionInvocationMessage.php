<?php

namespace Capevace\MagicImport\Prompt\Message;

use Capevace\MagicImport\Loop\Response\FunctionCall;
use Capevace\MagicImport\Prompt\Role;

readonly class FunctionInvocationMessage implements Message
{
    public function __construct(
        public Role $role,
        public FunctionCall $call
    )
    {
    }

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'function_call' => [
                'name' => $this->call->name,
                'arguments' => json_encode($this->call->arguments, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]
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
}
