<?php

namespace Capevace\MagicImport\Artifacts\Content;

interface Content
{
    public function toArray(): array;

    public static function from(array $data): static;

    public function getPage(): ?int;
}
