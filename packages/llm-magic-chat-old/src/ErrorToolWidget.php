<?php

namespace Mateffy\Magic\Chat;

use Illuminate\View\View;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;

class ErrorToolWidget extends ViewToolWidget
{
    public string $view = 'llm-magic::components.tools.error';

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
