<?php

namespace Mateffy\Magic\LLM\Message\MultimodalMessage;

use Mateffy\Magic\LLM\Message\WireableViaArray;

readonly class Text implements ContentInterface
{
    use WireableViaArray;

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

    public static function make(string $text): self
    {
        return new self(text: $text);
    }
}
