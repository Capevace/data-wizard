<?php

namespace Mateffy\Magic\Artifacts\Content;

readonly class ImageContent
{
    public function __construct(
        public string $path,
        public string $mimetype,
        public ?int $page = null,
        public ?int $width = null,
        public ?int $height = null,
    ) {}

    public function toArray(): array
    {
        return [
            'path' => $this->path,
            'mimetype' => $this->mimetype,
            'page' => $this->page,
        ];
    }

    public static function from(array $data): static
    {
        return new static(
            path: $data['path'],
            mimetype: $data['mimetype'],
            page: $data['page'] ?? null,
        );
    }
}
