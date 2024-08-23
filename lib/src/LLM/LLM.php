<?php

namespace Capevace\MagicImport\LLM;

use Capevace\MagicImport\Config\Organization;
use Capevace\MagicImport\LLM\Options\ElElEmOptions;
use Capevace\MagicImport\Model\ModelCost;
use Capevace\MagicImport\Prompt\Prompt;
use Closure;

interface LLM
{
    public function getOrganization(): Organization;
    public function getOptions(): ElElEmOptions;
    public function getModelName(): string;
    public function getModelCost(): ?ModelCost;

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null): array;
    public function send(Prompt $prompt): array;
}