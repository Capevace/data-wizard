<?php

namespace Mateffy\Magic\Builder\Concerns;

use Mateffy\Magic\Embeddings\EmbeddingModel;

trait HasEmbeddingModel
{
    public EmbeddingModel $model;

    public function model(string|EmbeddingModel $model): static
    {
        if ($model instanceof EmbeddingModel) {
            $this->model = $model;
        } else {
            $this->model = EmbeddingModel::fromString($model);
        }

        return $this;
    }
}
