<?php

namespace Mateffy\Magic\Builder;

use Mateffy\Magic\Builder\Concerns\HasEmbeddingModel;
use Mateffy\Magic\Builder\Concerns\HasInput;
use Mateffy\Magic\Builder\Concerns\HasTokenCallback;
use Mateffy\Magic\Embeddings\EmbeddedData;
use Mateffy\Magic\Embeddings\OpenAIEmbeddingModel;
use Mateffy\Magic\Magic;

class EmbeddingsBuilder
{
    use HasEmbeddingModel;
    use HasInput;
    use HasTokenCallback;

    public function get(): EmbeddedData
    {
        if ($this->getInput() === null) {
            throw new \InvalidArgumentException('You need to add the input value using ->input(...) to create an embedding.');
        }

        if (!isset($this->model)) {
            throw new \InvalidArgumentException('You need to set the model using ->model(...) to create an embedding.');
        }

        $input = $this->getInput();

        return $this->model->get($input);
    }
}
