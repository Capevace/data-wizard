<?php

namespace Capevace\MagicImport\Loop\Response;

readonly class FunctionCall
{
    public function __construct(
        public string $name,
        public array $arguments
    )
    {
    }
}