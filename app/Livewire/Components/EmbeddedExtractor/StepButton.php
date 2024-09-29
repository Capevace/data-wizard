<?php

namespace App\Livewire\Components\EmbeddedExtractor;

class StepButton
{
    public function __construct(
        public string $label,
        public string $icon,
        public string $color,
        public string $action,
        public bool $disabledWhileRunning = false,
    )
    {
    }
}
