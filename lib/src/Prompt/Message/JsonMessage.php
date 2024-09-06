<?php

namespace Capevace\MagicImport\Prompt\Message;

use Capevace\MagicImport\Prompt\Role;

readonly class JsonMessage implements Message
{
    public function __construct(
        public Role $role,
        public array $data
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'data' => $this->data,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new self(
            role: Role::tryFrom($data['role']) ?? Role::Assistant,
            data: $data['data'],
        );
    }
}
