<?php

namespace Mateffy\Magic\LLM\Message;

use Illuminate\Contracts\Support\Arrayable;
use Livewire\Wireable;

interface Message extends Arrayable, Wireable
{
    public static function fromArray(array $data): static;

    public function toArray(): array;

    public function text(): ?string;
}
