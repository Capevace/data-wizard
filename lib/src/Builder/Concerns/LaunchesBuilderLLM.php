<?php

namespace Capevace\MagicImport\Builder\Concerns;

use Capevace\MagicImport\LLM\PreconfiguredModelLaunchInterface;

trait LaunchesBuilderLLM
{
    public function build(): PreconfiguredModelLaunchInterface
    {
        return $this;
    }
}
