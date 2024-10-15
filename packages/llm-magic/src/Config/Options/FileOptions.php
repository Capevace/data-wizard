<?php

namespace Mateffy\Magic\Config\Options;

readonly class FileOptions
{
    public function __construct(
        public ?float $maxSizeInMegabytes = 10,
        public ?int $maxNumberOfFiles = null,
    ) {}

    public function withOptions(array $data): static
    {
        return new static(
            maxSizeInMegabytes: $data['maxSize'] ?? $this->maxSizeInMegabytes,
            maxNumberOfFiles: $data['maxNumberOfFiles'] ?? $this->maxNumberOfFiles,
        );
    }
}
