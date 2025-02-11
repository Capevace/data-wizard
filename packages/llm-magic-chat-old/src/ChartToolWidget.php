<?php

namespace Mateffy\Magic\Chat;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;

class ChartToolWidget extends ViewToolWidget
{
    public string $view = 'llm-magic::components.tools.dropdown-chart';

    protected Closure $label;
    protected Closure $description;
    protected Closure $icon;
    protected Closure $chevronIcon;
    protected Closure $color;
    protected Closure $chartJsConfig;


    public function __construct(
        ?Closure $label = null,
        ?Closure $description = null,
        ?Closure $icon = null,
        ?Closure $color = null,
        ?Closure $chartJsConfig = null,
        protected bool $useOutput = true,
    )
    {
        $get = fn (string $key, mixed $default) =>
            fn (FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output) =>
                Arr::get($this->useOutput ? $output?->output : $invocation->call?->arguments ?? [], $key, $default);

        $this->label = $label ?? $get('label', __('Map'));
        $this->description = $description ?? $get('description', null);
        $this->icon = $icon ?? $get('icon', 'heroicon-o-chart-pie');
        $this->chevronIcon = $chevronIcon ?? $get('chevronIcon', 'heroicon-o-chevron-down');
        $this->color = $color ?? $get('color', 'gray');
        $this->chartJsConfig = $chartJsConfig ?? $get('chart_js_config', []);

        parent::__construct($this->view);
    }

    protected function prepare(InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output): View
    {
        $app = app();
        $make = fn (Closure $fn) => $app->call($fn, ['tool' => $tool, 'invocation' => $invocation, 'output' => $output]);

        return parent::prepare($tool, $invocation, $output)
            ->with('label', $make($this->label))
            ->with('description', $make($this->description))
            ->with('icon', $make($this->icon))
            ->with('chevronIcon', $make($this->chevronIcon))
            ->with('color', $make($this->color))
            ->with('chartJsConfig', $make($this->chartJsConfig));
    }
}
