<?php

namespace Capevace\MagicImport\Prompt\Message;

use Capevace\MagicImport\Prompt\Role;

readonly class TextMessage implements Message
{
    public function __construct(
        public Role $role,
        public string $content
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'content' => $this->content,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new self(
            role: Role::tryFrom($data['role']) ?? Role::Assistant,
            content: $data['content'],
        );
    }
}
