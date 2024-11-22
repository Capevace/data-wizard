<?php

namespace Mateffy\Magic\LLM\Message;

use Illuminate\Contracts\Support\Arrayable;
use Livewire\Wireable;
use Mateffy\Magic\Prompt\Role;

interface Message extends Arrayable, Wireable
{
    public static function fromArray(array $data): static;

    public function toArray(): array;

    public function text(): ?string;
    public function role(): Role;
}
