<?php

namespace App\Livewire\Components\Concerns;

class ConfirmationToolWidget extends ViewToolWidget
{
    public string $view = 'components.tools.confirmation';

    public function __construct(string $text)
    {
        parent::__construct($this->view, ['text' => $text]);
    }

    public function interrupts(): bool
    {
        return true;
    }
}
