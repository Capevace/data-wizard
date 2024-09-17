<?php

namespace Capevace\MagicImport\Builder\Concerns;

trait HasSystemPrompt
{
    public ?string $systemPrompt = null;

    public function system(?string $prompt): static
    {
        $this->systemPrompt = $prompt;

        return $this;
    }
}
