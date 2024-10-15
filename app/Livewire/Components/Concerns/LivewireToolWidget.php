<?php

namespace App\Livewire\Components\Concerns;

class LivewireToolWidget extends ToolWidget
{
    public function __construct(
        public string $component,
        public array $props = [],
    ) {}
}
