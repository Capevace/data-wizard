<?php

namespace App\Extraction\Artifacts\Slices;

interface TextualSlice extends Slice
{
    public function text(): string;
}
