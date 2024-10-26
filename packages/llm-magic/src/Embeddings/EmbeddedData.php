<?php

namespace Mateffy\Magic\Embeddings;

readonly class EmbeddedData
{
    public function __construct(
        public array $vectors,
    )
    {
    }
}
