<?php

namespace Capevace\MagicImport\LLM;

use Closure;

interface PreconfiguredModelLaunchInterface
{
    public function stream(?Closure $onMessageProgress = null, ?Closure $onMessage = null): array;

    public function send(): array;
}
