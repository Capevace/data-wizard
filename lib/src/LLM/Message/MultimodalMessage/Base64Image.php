<?php

namespace Mateffy\Magic\LLM\Message\MultimodalMessage;

use Mateffy\Magic\LLM\Message\WireableViaArray;

readonly class Base64Image implements ContentInterface
{
    use WireableViaArray;

    public function __construct(
        public string $imageBase64,
        public string $mime,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => 'image',
            'source' => [
                'type' => 'base64',
                'media_type' => $this->mime,
                'data' => $this->imageBase64,
            ],
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            imageBase64: $data['source']['data'],
            mime: $data['source']['media_type'],
        );
    }

    public static function fromPath(string $path): self
    {
        $mime = mime_content_type($path);

        return new self(
            imageBase64: base64_encode(file_get_contents($path)),
            mime: $mime,
        );
    }
}
