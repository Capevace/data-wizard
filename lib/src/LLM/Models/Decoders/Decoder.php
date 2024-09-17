<?php

namespace Capevace\MagicImport\LLM\Models\Decoders;

interface Decoder
{
    public function process(): array;
}
