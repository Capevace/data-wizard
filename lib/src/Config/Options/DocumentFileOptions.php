<?php

namespace Capevace\MagicImport\Config\Options;

readonly class DocumentFileOptions extends FileOptions
{
    public function __construct(
        public ?float $maxSizeInMegabytes = 10,
        public ?int $maxNumberOfFiles = null,
        public bool $extractImages = true,
    )
    {
        parent::__construct(maxSizeInMegabytes: $maxSizeInMegabytes, maxNumberOfFiles: $maxNumberOfFiles);
    }

    public function withOptions(array $data): static
    {
        return new static(
            maxSizeInMegabytes: $data['maxSize'] ?? $this->maxSizeInMegabytes,
            maxNumberOfFiles: $data['maxNumberOfFiles'] ?? $this->maxNumberOfFiles,
            extractImages: $data['extractImages'] ?? $this->extractImages,
        );
    }
}