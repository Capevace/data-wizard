<?php

namespace Mateffy\Magic\Artifacts\Content;

interface Content
{
    public function toArray(): array;

    public static function from(array $data): static;
}
