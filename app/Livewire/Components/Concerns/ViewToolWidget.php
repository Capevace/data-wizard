<?php

namespace App\Livewire\Components\Concerns;

use Illuminate\View\View;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\Message;
use Throwable;

class ViewToolWidget extends ToolWidget
{
    public function __construct(
        public string $view,
        public array $with = [],
    ) {}

    protected function prepare(InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output): View
    {
        return view($this->view, $this->with)
            ->with('tool', $tool)
            ->with('invocation', $invocation)
            ->with('output', $output);
    }

    /**
     * @throws Throwable
     */
    public function render(InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output = null): string
    {
        return $this->prepare($tool, $invocation, $output)->render() ?? '';
    }
}
