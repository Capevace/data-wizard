<?php

namespace Capevace\MagicImport\LLM\Message;

use Capevace\MagicImport\LLM\Message\MultimodalMessage\Base64Image;
use Capevace\MagicImport\LLM\Message\MultimodalMessage\Text;
use Capevace\MagicImport\Prompt\Role;

class MultimodalMessage implements Message
{
    public function __construct(
        public Role $role,
        /** @var array<Base64Image|Text> */
        public array $content
    ) {}

    public function toArray(): array
    {
        return [
            'role' => $this->role->value,
            'content' => array_map(fn (Base64Image|Text $item) => $item->toArray(), $this->content),
        ];
    }

    public static function fromArray(array $data): static
    {
        return new self(
            role: Role::tryFrom($data['role']) ?? Role::Assistant,
            content: collect($data['content'])
                ->map(fn (array $item) => match ($item['type']) {
                    'text' => Text::fromArray($item),
                    'image' => Base64Image::fromArray($item),
                    default => null,
                })
                ->filter()
                ->all()
        );
    }

    public function text(): ?string
    {
        if (! is_string($this->content)) {
            return json_encode($this->content, JSON_THROW_ON_ERROR);
        }

        return $this->content;
    }
}
