<?php

namespace Mateffy\Magic\LLM;

use Closure;
use Mateffy\Magic\Prompt\Prompt;

interface ModelLaunchInterface
{
    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): MessageCollection;

    public function send(Prompt $prompt): MessageCollection;
}
