<?php

namespace App\Livewire\Components\Concerns;

use App\PGRoll;
use Closure;
use Illuminate\Container\Container;
use Illuminate\View\View;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;

class LoadingToolWidget extends ViewToolWidget
{
    public string $view = 'components.tools.loaders.detailed';

    protected Closure $loading;
    protected Closure $done;

    protected Closure $loadingIcon;
    protected Closure $loadingIconColor;
    protected Closure $doneIcon;
    protected Closure $doneIconColor;

    public function __construct(
        ?Closure $loading = null,
        ?Closure $done = null,
        ?Closure $loadingIcon = null,
        ?Closure $loadingIconColor = null,
        ?Closure $doneIcon = null,
        ?Closure $doneIconColor = null,
        array $with = []
    )
    {
        $this->loading = $loading ?? fn (FunctionInvocationMessage $invocation) => "Running `" . str($invocation->call->name)->snake()->replace('_', ' ')->title() . '`...';
        $this->done = $done ?? fn () => 'Done';
        $this->loadingIcon = $loadingIcon ?? fn () => 'filament::loading-indicator';
        $this->loadingIconColor = $loadingIconColor ?? fn () => 'gray';
        $this->doneIcon = $doneIcon ?? fn () => 'heroicon-o-check-circle';
        $this->doneIconColor = $doneIconColor ?? fn () => 'success';

        parent::__construct($this->view, $with);
    }

    protected function prepare(InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output): View
    {
        $fn = $output === null
            ? $this->loading
            : $this->done;

        $callWithArguments = fn (Closure $fn) => app()->call($fn, [
            'tool' => $tool,
            'invocation' => $invocation,
            'output' => $output,
        ]);

        $content = $callWithArguments($fn);

        return parent::prepare($tool, $invocation, $output)
            ->with('content', $content)
            ->with('loadingIcon', $callWithArguments($this->loadingIcon))
            ->with('loadingIconColor', $callWithArguments($this->loadingIconColor))
            ->with('doneIcon', $callWithArguments($this->doneIcon))
            ->with('doneIconColor', $callWithArguments($this->doneIconColor));
    }
}
