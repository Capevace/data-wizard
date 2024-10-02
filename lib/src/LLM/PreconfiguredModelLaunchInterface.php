<?php

namespace Mateffy\Magic\LLM;

use Closure;
use Illuminate\Support\Collection;

interface PreconfiguredModelLaunchInterface
{
    public function stream(?Closure $onMessageProgress = null, ?Closure $onMessage = null): Collection;

    public function send(): Collection;
}
