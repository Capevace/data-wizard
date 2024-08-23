<?php

namespace Capevace\MagicImport\Prompt\Message;

use Illuminate\Contracts\Support\Arrayable;

interface Message extends Arrayable
{
    public static function fromArray(array $data): static;
    public function toArray(): array;
}
