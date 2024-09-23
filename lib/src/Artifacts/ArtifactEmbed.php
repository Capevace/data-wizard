<?php

namespace Mateffy\Magic\Artifacts;

readonly class ArtifactEmbed
{
    public function __construct(
        public string $filename,
        public string $name,
        public string $mimetype,
        public ?int $page = null,
    ) {}

    public function toArray(): array
    {
        return [
            'filename' => $this->filename,
            'name' => $this->name,
            'mimetype' => $this->mimetype,
            'page' => $this->page,
        ];
    }

    public static function from(array $data): static
    {
        return new static(
            filename: $data['filename'],
            name: $data['name'],
            mimetype: $data['mimetype'],
            page: $data['page'] ?? null,
        );
    }
}
