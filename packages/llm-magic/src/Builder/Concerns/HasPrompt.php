<?php

namespace Mateffy\Magic\Builder\Concerns;

use Mateffy\Magic\Prompt\Prompt;

trait HasPrompt
{
    public ?Prompt $prompt = null;

    public function prompt(?Prompt $prompt): static
    {
        $this->prompt = $prompt;

        return $this;
    }
}
