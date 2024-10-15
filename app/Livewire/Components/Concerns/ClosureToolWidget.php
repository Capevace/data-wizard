<?php

namespace App\Livewire\Components\Concerns;

use Closure;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Throwable;

class ClosureToolWidget extends ToolWidget
{
    public function __construct(
        /** @var Closure(InvokableFunction, FunctionInvocationMessage, ?FunctionOutputMessage): string $render */
        public Closure $render,
    ) {}

    /**
     * @throws Throwable
     */
    public function render(InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output = null): string
    {
        return app()->call($this->render, ['tool' => $tool, 'invocation' => $invocation, 'output' => $output]) ?? '';
    }
}
