<?php

namespace Capevace\MagicImport\LLM;

use Capevace\MagicImport\Config\Organization;
use Capevace\MagicImport\LLM\Options\ElElEmOptions;
use Capevace\MagicImport\Model\ModelCost;

interface LLM extends ModelLaunchInterface
{
    public function withOptions(array $data): static;

    public function getOrganization(): Organization;

    public function getOptions(): ElElEmOptions;

    public function getModelName(): string;

    public function getModelCost(): ?ModelCost;
}
