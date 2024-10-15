<?php

namespace App\Livewire\Components\Concerns;

use Closure;
use Illuminate\View\View;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;

class TableToolWidget extends ViewToolWidget
{
    public string $view = 'components.tools.dropdown-table';

    protected Closure $label;
    protected Closure $description;
    protected Closure $icon;
    protected Closure $chevronIcon;
    protected Closure $color;
    protected Closure $columns;
    protected Closure $rows;

    public function __construct(
        ?Closure $label = null,
        ?Closure $description = null,
        ?Closure $icon = null,
        ?Closure $chevronIcon = null,
        ?Closure $color = null,
        ?Closure $columns = null,
        ?Closure $rows = null,
    )
    {
        $this->label = $label ?? fn (FunctionInvocationMessage $invocation) => $invocation->call?->arguments['label'] ?? __('Table');
        $this->description = $description ?? fn (FunctionInvocationMessage $invocation) => $invocation->call?->arguments['description'] ?? null;
        $this->icon = $icon ?? fn (FunctionInvocationMessage $invocation) => $invocation->call?->arguments['icon'] ?? null;
        $this->chevronIcon = $chevronIcon ?? fn (FunctionInvocationMessage $invocation) => $invocation->call?->arguments['chevronIcon'] ?? 'heroicon-o-chevron-down';
        $this->color = $color ?? fn (FunctionInvocationMessage $invocation) => $invocation->call?->arguments['color'] ?? 'gray';
        $this->columns = $columns ?? fn (FunctionInvocationMessage $invocation) => $invocation->call?->arguments['columns'] ?? [];
        $this->rows = $rows ?? fn (FunctionInvocationMessage $invocation) => $invocation->call?->arguments['rows'] ?? [];

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
            ->with('columns', $make($this->columns))
            ->with('rows', $make($this->rows));
    }

    public function interrupts(): bool
    {
        return true;
    }
}
