<?php

namespace Capevace\MagicImport\Loop\Response\Streamed;

use Capevace\MagicImport\Loop\Response\LLMResponse;
use Capevace\MagicImport\Loop\Response\TextResponse;
use Capevace\MagicImport\Prompt\Role;

class PartialTextResponse implements PartialResponse
{
    public function __construct(
        public readonly Role $role,
        public string $content
    )
    {
    }

    public function append(string $content): static
    {
        $this->content .= $content;

        return $this;
    }

    public function toResponse(): LLMResponse
    {
        return new TextResponse($this->content);
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function copy(): static
    {
        return new static($this->role, $this->content);
    }
}