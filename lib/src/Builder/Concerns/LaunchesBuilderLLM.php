<?php

namespace Mateffy\Magic\Builder\Concerns;

use Mateffy\Magic\LLM\PreconfiguredModelLaunchInterface;

trait LaunchesBuilderLLM
{
    public function build(): PreconfiguredModelLaunchInterface
    {
        return $this;
    }
}
