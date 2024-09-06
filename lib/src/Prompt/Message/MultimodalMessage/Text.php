<?php

namespace Capevace\MagicImport\Prompt\Message\MultimodalMessage;

readonly class Text
{
    public function __construct(
        public string $text
    ) {}

    public function toArray(): array
    {
        return [
            'type' => 'text',
            'text' => $this->text,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            text: $data['text'],
        );
    }
}
