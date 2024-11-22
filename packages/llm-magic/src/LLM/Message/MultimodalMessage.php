<?php

namespace Mateffy\Magic\LLM\Message;

use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;
use Mateffy\Magic\LLM\Message\MultimodalMessage\ContentInterface;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Text;
use Mateffy\Magic\Prompt\Role;

class MultimodalMessage implements Message
{
    use WireableViaArray;

    public function __construct(
        public Role $role,
        /** @var array<ContentInterface> */
        public array $content
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'content' => array_map(fn (ContentInterface $item) => $item->toArray(), $this->content),
        ];
    }

    public static function fromArray(array $data): static
    {
        return new self(
            role: Role::tryFrom($data['role']) ?? Role::Assistant,
            content: collect($data['content'])
                ->map(fn (array $item) => match ($item['type'] ?? null) {
                    'text' => Text::fromArray($item),
                    'image' => Base64Image::fromArray($item),
                    default => null,
                })
                ->filter()
                ->all()
        );
    }

    public function text(): ?string
    {
        if (! is_string($this->content)) {
            return json_encode($this->content, JSON_THROW_ON_ERROR);
        }

        return $this->content;
    }

    /**
     * @param array<ContentInterface> $content
     */
    public static function user(array $content): static
    {
        return new self(
            role: Role::User,
            content: $content,
        );
    }

    public static function assistant(array $content): static
    {
        return new self(
            role: Role::Assistant,
            content: $content,
        );
    }

    /**
     * @param array<ContentInterface> $content
     */
    public static function base64Image(string $base64Image, string $mime): static
    {
        return new self(
            role: Role::User,
            content: [
                new Base64Image(
                    imageBase64: $base64Image,
                    mime: $mime,
                ),
            ],
        );
    }

    public function role(): Role
    {
        return $this->role;
    }
}
