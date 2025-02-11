<?php

namespace Mateffy\Magic\Chat;

class ConfirmationToolWidget extends ViewToolWidget
{
    public string $view = 'llm-magic::components.tools.confirmation';

    public function __construct(string $text)
    {
        parent::__construct($this->view, ['text' => $text]);
    }

    public function interrupts(): bool
    {
        return true;
    }
}
