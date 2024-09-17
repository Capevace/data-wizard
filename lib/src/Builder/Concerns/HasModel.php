<?php

namespace Capevace\MagicImport\Builder\Concerns;

use Capevace\MagicImport\LLM\ElElEm;
use Capevace\MagicImport\LLM\LLM;

trait HasModel
{
    public LLM $model;

    public function model(string|LLM $model): static
    {
        if ($model instanceof LLM) {
            $this->model = $model;
        } else {
            $this->model = ElElEm::fromString($model);
        }

        return $this;
    }
}
