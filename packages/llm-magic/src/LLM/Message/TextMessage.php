<?php

namespace Mateffy\Magic\LLM\Message;

use Mateffy\Magic\Prompt\Role;

class TextMessage implements Message, PartialMessage
{
    use WireableViaArray;

    public function __construct(
        public Role $role,
        public string $content,
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

    public function text(): ?string
    {
        return $this->content;
    }

    public function append(string $chunk): static
    {
        $this->content .= $chunk;

        return $this;
    }

    public static function fromChunk(string $chunk): static
    {
        return new self(
            role: Role::Assistant,
            content: $chunk,
        );
    }

    public static function user(string $content): static
    {
        return new self(
            role: Role::User,
            content: $content,
        );
    }

    public static function assistant(string $content): static
    {
        return new self(
            role: Role::Assistant,
            content: $content,
        );
    }

    public function role(): Role
    {
        return $this->role;
    }
}
