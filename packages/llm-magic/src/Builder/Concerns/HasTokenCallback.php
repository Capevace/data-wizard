<?php

namespace Mateffy\Magic\Builder\Concerns;

use Closure;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\Prompt\TokenStats;

trait HasTokenCallback
{
    protected ?Closure $onTokenStats = null;

    /**
     * @param ?Closure(TokenStats): void $onTokenStats
     */
    public function onTokenStats(?Closure $onTokenStats): static
    {
        $this->onTokenStats = $onTokenStats;

        return $this;
    }
}
