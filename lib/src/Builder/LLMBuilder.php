<?php

namespace Capevace\MagicImport\Builder;

use Capevace\MagicImport\LLM\PreconfiguredModelLaunchInterface;
use Capevace\MagicImport\Prompt\Prompt;

interface LLMBuilder extends PreconfiguredModelLaunchInterface
{
    public function build(): Prompt;
}
