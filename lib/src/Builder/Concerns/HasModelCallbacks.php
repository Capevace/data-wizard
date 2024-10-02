<?php

namespace Mateffy\Magic\Builder\Concerns;

use Closure;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\Prompt\TokenStats;

trait HasModelCallbacks
{
    protected ?Closure $onMessageProgress = null;
    protected ?Closure $onMessage = null;
    protected ?Closure $onTokenStats = null;

    /**
     * @param ?Closure(Message): void $onMessageProgress
     */
    public function onMessageProgress(?Closure $onMessageProgress): static
    {
        $this->onMessageProgress = $onMessageProgress;

        return $this;
    }

    /**
     * @param ?Closure(Message): void $onMessage
     */
    public function onMessage(?Closure $onMessage): static
    {
        $this->onMessage = $onMessage;

        return $this;
    }

    /**
     * @param ?Closure(TokenStats): void $onTokenStats
     */
    public function onTokenStats(?Closure $onTokenStats): static
    {
        $this->onTokenStats = $onTokenStats;

        return $this;
    }
}
