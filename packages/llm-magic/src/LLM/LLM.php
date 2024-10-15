<?php

namespace Mateffy\Magic\LLM;

use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\Options\ElElEmOptions;

interface LLM extends ModelLaunchInterface
{
    public function withOptions(array $data): static;

    public function getOrganization(): Organization;

    public function getOptions(): ElElEmOptions;

    public function getModelName(): string;

    public function getModelCost(): ?ModelCost;
}
