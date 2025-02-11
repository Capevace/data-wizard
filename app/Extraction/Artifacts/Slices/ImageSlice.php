<?php

namespace App\Extraction\Artifacts\Slices;

readonly class ImageSlice implements Slice, EmbedSlice
{
    public function __construct(
        public string $mimetype,
        public string $path,
        public ?int $page = null,
        public ?int $width = null,
        public ?int $height = null,
        public bool $absolutePath = false,
    ) {
    }

    public function toArray(): array
    {
        return [
            'mimetype' => $this->mimetype,
            'path' => $this->path,
            'page' => $this->page,
            'width' => $this->width,
            'height' => $this->height,
        ];
    }

    public static function from(array $data): static
    {
        return new static(
            mimetype: $data['mimetype'],
            path: $data['path'],
            page: $data['page'] ?? null,
            width: $data['width'] ?? null,
            height: $data['height'] ?? null,
        );
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMimeType(): string
    {
        return $this->mimetype;
    }

    public function isAbsolutePath(): string
    {
        return $this->absolutePath;
    }
}
