<?php

namespace Mateffy\Magic\LLM\Message;

use Illuminate\Contracts\Support\Arrayable;

interface Message extends Arrayable
{
    public static function fromArray(array $data): static;

    public function toArray(): array;

    public function text(): ?string;
}
