<?php

namespace Mateffy\Magic\Chat;

use Closure;
use Illuminate\Support\Facades\Blade;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;


class LivewireToolWidget extends ToolWidget
{
    public function __construct(
        public Closure $name,
        public Closure $props
    ) {}

    public function render(InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output = null): string
    {;
        return Blade::render(<<<'BLADE'
            @livewire($name, $props)
        BLADE, [
            'name' => app()->call($this->name, [
                'tool' => $tool,
                'invocation' => $invocation,
                'output' => $output,
            ]),
            'props' => app()->call($this->props, [
                'tool' => $tool,
                'invocation' => $invocation,
                'output' => $output,
            ]),
        ]);
    }
}
