<?php

namespace Mateffy\Magic\LLM\Models\Decoders;

interface Decoder
{
    public function process(): array;
}
