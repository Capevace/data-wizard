<?php

namespace Mateffy\Magic\Builder\Concerns;

trait HasSystemPrompt
{
    public ?string $systemPrompt = null;

    public function system(?string $prompt): static
    {
        $this->systemPrompt = $prompt;

        return $this;
    }
}
