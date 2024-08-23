<?php

namespace Capevace\MagicImport\Artifacts\Content;

use Illuminate\Support\Facades\Log;

readonly class ImageContent
{
    public function __construct(
        public string $filename,
        public string $mimetype,
        public ?int $page = null,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'filename' => $this->filename,
            'mimetype' => $this->mimetype,
            'page' => $this->page,
        ];
    }

    public static function from(array $data): static
    {
        Log::debug('ImageContent::from', [$data]);
        return new static(
            filename: $data['filename'],
            mimetype: $data['mimetype'],
            page: $data['page'] ?? null,
        );
    }
}