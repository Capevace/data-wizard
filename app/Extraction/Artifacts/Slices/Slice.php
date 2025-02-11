<?php

namespace App\Extraction\Artifacts\Slices;

interface Slice
{
    public function toArray(): array;

    public static function from(array $data): static;

    public function getPage(): ?int;
}
