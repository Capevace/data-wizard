<?php

namespace Mateffy\Magic\Config;

use Mateffy\Magic\Config\Options\DocumentFileOptions;
use Mateffy\Magic\Config\Options\FileOptions;

class ExtractorFileType
{
    public const IMAGES = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    public const DOCUMENTS = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    public function __construct(
        public array $mimetypes,
        public ?FileOptions $options = null,
    ) {}

    public function withOptions(array $options): static
    {
        return new static(
            mimetypes: $this->mimetypes,
            options: $this->options?->withOptions($options),
        );
    }

    public static function fromString(string $type): ExtractorFileType
    {
        $mimetype = mime_content_type($type);

        // Support configuring using just the mimetype (e.g. image/jpeg or application/pdf)
        $fileType = self::tryFromMimetype($mimetype);

        // Support configuring using a preset (images, documents)
        $fileType ??= self::tryFromPreset($type);

        if ($fileType === null) {
            throw new \InvalidArgumentException("Invalid file type: {$type}");
        }

        return $fileType;
    }

    public static function fromArray(array $data): ?ExtractorFileType
    {
        if ($data['type'] ?? null === null) {
            throw new \InvalidArgumentException('Missing type key in file type data');
        }

        $mimetype = mime_content_type($data['type']);

        // Get the correctly typed file type
        $fileType = self::fromString($mimetype);

        // Then let it apply its options
        return $fileType->withOptions($data);
    }

    protected static function tryFromMimetype(string $mimetype): ?ExtractorFileType
    {
        $isImage = in_array($mimetype, self::IMAGES);
        $isDocument = in_array($mimetype, self::DOCUMENTS);

        if (! $isImage && ! $isDocument) {
            return null;
        }

        return new ExtractorFileType(
            mimetypes: [$mimetype],
            options: match (true) {
                $isDocument => new DocumentFileOptions,
                default => new FileOptions,
            }
        );
    }

    protected static function tryFromPreset(string $preset): ?ExtractorFileType
    {
        return match ($preset) {
            'images' => new ExtractorFileType(
                mimetypes: self::IMAGES,
                options: new FileOptions,
            ),
            'documents' => new ExtractorFileType(
                mimetypes: self::DOCUMENTS,
                options: new DocumentFileOptions,
            ),
            default => null,
        };
    }
}
