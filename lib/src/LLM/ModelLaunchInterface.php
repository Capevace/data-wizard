<?php

namespace Mateffy\Magic\LLM;

use Mateffy\Magic\Prompt\Prompt;
use Closure;

interface ModelLaunchInterface
{
    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null): array;

    public function send(Prompt $prompt): array;
}
