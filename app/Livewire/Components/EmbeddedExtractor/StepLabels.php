<?php

namespace App\Livewire\Components\EmbeddedExtractor;

class StepLabels
{
    public function __construct(
        public string $heading,
        public ?string $description = null,
        public ?StepButton $backButton = null,
        public ?StepButton $nextButton = null,
        public ?StepButton $secondaryButton = null,
    ) {}
}
