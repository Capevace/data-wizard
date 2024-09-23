<?php

namespace Mateffy\Magic\Artifacts;

class ArtifactMetadata
{
    public function __construct(
        public string $name,
        public string $mimetype,
        public string $extension,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'mimetype' => $this->mimetype,
            'extension' => $this->extension,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name'],
            mimetype: $data['mimetype'],
            extension: $data['extension'],
        );
    }

    public static function fromPath(string $jsonPath): static
    {
        $contents = file_get_contents($jsonPath);
        $data = json_decode($contents, flags: JSON_THROW_ON_ERROR, associative: true);

        return static::fromArray($data);
    }
}
