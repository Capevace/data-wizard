<?php

namespace Capevace\MagicImport\Model;

use Capevace\MagicImport\Prompt\Prompt;
use Closure;

interface LLM
{
    public function name(): string;

    public function parameters(): array;

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null): array;

    public function send(Prompt $prompt): array;

    public function cost(): ?ModelCost;
}
