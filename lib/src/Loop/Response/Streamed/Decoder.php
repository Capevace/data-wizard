<?php

namespace Capevace\MagicImport\Loop\Response\Streamed;

interface Decoder
{
    public function process(): array;
}
