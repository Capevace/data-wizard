<?php

namespace Mateffy\Magic\Artifacts\Content;

readonly class TextContent implements Content
{
    public function __construct(
        public string $text,
        public array $embeds = [],
        public ?int $page = null,
    ) {}

    public function toArray(): array
    {
        return [
            'text' => $this->text,
            'embeds' => $this->embeds,
            'page' => $this->page,
        ];
    }

    public static function from(array $data): static
    {
        return new static(
            text: $data['text'],
            embeds: $data['embeds'] ?? [],
            page: $data['page'] ?? null,
        );
    }
}
