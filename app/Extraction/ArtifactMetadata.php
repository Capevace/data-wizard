<?php

namespace App\Extraction;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ArtifactMetadata
{
    public function __construct(
        public ArtifactType $type,
        public string $path,
        public string $name,
        public string $mimetype,
        public string $extension,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'path' => $this->path,
            'name' => $this->name,
            'mimetype' => $this->mimetype,
            'extension' => $this->extension,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static(
            type: ArtifactType::from($data['type']),
            path: $data['path'],
            name: $data['name'],
            mimetype: $data['mimetype'],
            extension: $data['extension'],
        );
    }

    public static function fromPath(string $jsonPath): static
    {
        $data = File::json($jsonPath, flags: JSON_THROW_ON_ERROR);

        return static::fromArray($data);
    }

    public static function fromFile(string $path, ?string $disk = null): static
    {
        $mimeType = $disk
            ? Storage::disk($disk)->mimeType($path)
            : mime_content_type($path);

        return new self(
            type: ArtifactType::fromMimetype($mimeType),
            path: $path,
            name: basename($path),
            mimetype: $mimeType,
            extension: pathinfo($path, PATHINFO_EXTENSION),
        );
    }
}
