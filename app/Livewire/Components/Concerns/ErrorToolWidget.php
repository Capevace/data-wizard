<?php

namespace App\Livewire\Components\Concerns;

use Illuminate\View\View;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\Message;
use Throwable;

class ErrorToolWidget extends ViewToolWidget
{
    public string $view = 'components.tools.error';

    public function __construct(
        public string $error,
        public ?string $details = null,
        public array $with = [],
    ) {
        parent::__construct($this->view, $with);
    }

    protected function prepare(InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output): View
    {
        return parent::prepare($tool, $invocation, $output)
            ->with('error', $this->error)
            ->with('details', $this->details);
    }
}
