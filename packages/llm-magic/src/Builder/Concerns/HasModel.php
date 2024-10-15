<?php

namespace Mateffy\Magic\Builder\Concerns;

use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\LLM;

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
