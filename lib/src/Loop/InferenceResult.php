<?php

namespace Capevace\MagicImport\Loop;

readonly class InferenceResult
{
    public function __construct(
        public mixed $value,
        public array $messages,
    )
    {
    }
}