<?php

namespace App\Extraction\Artifacts\Slices;

readonly class RawTextSlice implements Slice, TextualSlice
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

    public function text(): string
    {
        return $this->text;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }
}
