<?php

namespace Mateffy\Magic\Chat;

use Closure;
use Illuminate\Support\Js;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Throwable;

abstract class ToolWidget
{
    /**
     * @throws Throwable
     */
    abstract public function render(InvokableFunction $tool, FunctionInvocationMessage $invocation, ?FunctionOutputMessage $output = null): string;

    public function interrupts(): bool
    {
        return false;
    }

    public static function livewire(Closure|string $name, Closure|array $props = []): LivewireToolWidget
    {
        if (is_string($name)) {
            $name = fn () => $name;
        }

        if (is_array($props)) {
            $props = fn () => $props;
        }

        return app(LivewireToolWidget::class, ['name' => $name, 'props' => $props]);
    }

    public static function view(string $view, array $with = []): ViewToolWidget
    {
        return app(ViewToolWidget::class, ['view' => $view, 'with' => $with]);
    }

    /**
     * @param Closure(InvokableFunction, FunctionInvocationMessage, ?FunctionOutputMessage): string $render
     */
    public static function closure(Closure $render): ClosureToolWidget
    {
        return app(ClosureToolWidget::class, ['render' => $render]);
    }

    public static function confirmation(string $text): ConfirmationToolWidget
    {
        return app(ConfirmationToolWidget::class, ['text' => $text]);
    }

    public static function loading(
        Closure|string|null $loading = null,
        Closure|string|null $done = null,
        Closure|string|null $loadingIcon = null,
        Closure|string|null $loadingIconColor = null,
        Closure|string|null $doneIcon = null,
        Closure|string|null $doneIconColor = null,
        array $with = [],
        Closure|string $variant = 'default'
    ): LoadingToolWidget
    {
        if (is_string($loading)) {
            $loading = fn () => $loading;
        }

        if (is_string($done)) {
            $done = fn () => $done;
        }

        if (is_string($loadingIcon)) {
            $loadingIcon = fn () => $loadingIcon;
        }

        if (is_string($loadingIconColor)) {
            $loadingIconColor = fn () => $loadingIconColor;
        }

        if (is_string($doneIcon)) {
            $doneIcon = fn () => $doneIcon;
        }

        if (is_string($doneIconColor)) {
            $doneIconColor = fn () => $doneIconColor;
        }

        if (is_string($variant)) {
            $variant = fn () => $variant;
        }

        return app(LoadingToolWidget::class, [
            'loading' => $loading,
            'done' => $done,
            'loadingIcon' => $loadingIcon,
            'loadingIconColor' => $loadingIconColor,
            'doneIcon' => $doneIcon,
            'doneIconColor' => $doneIconColor,
            'with' => $with,
            'variant' => $variant
        ]);
    }

    public static function table(
        Closure|string|null $label = null,
        Closure|string|null $description = null,
        Closure|string|null $icon = null,
        Closure|string|null $chevronIcon = null,
        Closure|string|null $color = null,
        Closure|array|null $columns = null,
        Closure|array|null $rows = null,
    )
    {
        if (is_string($label)) {
            $label = fn () => $label;
        }

        if (is_string($description)) {
            $description = fn () => $description;
        }

        if (is_string($icon)) {
            $icon = fn () => $icon;
        }

        if (is_string($chevronIcon)) {
            $chevronIcon = fn () => $chevronIcon;
        }

        if (is_string($color)) {
            $color = fn () => $color;
        }

        if (is_array($columns)) {
            $columns = fn () => $columns;
        }

        if (is_array($rows)) {
            $rows = fn () => $rows;
        }

        return app(TableToolWidget::class, [
            'label' => $label,
            'description' => $description,
            'icon' => $icon,
            'chevronIcon' => $chevronIcon,
            'color' => $color,
            'columns' => $columns,
            'rows' => $rows,
        ]);
    }

    public static function chart(
        Closure|string|null $label = null,
        Closure|string|null $description = null,
        Closure|string|null $icon = null,
        Closure|string|null $chevronIcon = null,
        Closure|string|null $color = null,
        Closure|array|null $chartJsConfig = null,
    )
    {
        if (is_string($label)) {
            $label = fn () => $label;
        }

        if (is_string($description)) {
            $description = fn () => $description;
        }

        if (is_string($icon)) {
            $icon = fn () => $icon;
        }

        if (is_string($chevronIcon)) {
            $chevronIcon = fn () => $chevronIcon;
        }

        if (is_string($color)) {
            $color = fn () => $color;
        }

        if (is_array($chartJsConfig)) {
            $chartJsConfig = fn () => $chartJsConfig;
        }

        return app(ChartToolWidget::class, [
            'label' => $label,
            'description' => $description,
            'icon' => $icon,
            'chevronIcon' => $chevronIcon,
            'color' => $color,
            'chartJsConfig' => $chartJsConfig,
        ]);
    }

    public static function map(
        Closure|array|null $center = null,
        Closure|int|null $zoom = null,
        Closure|array|null $markers = null,
        Closure|string|null $label = null,
        Closure|string|null $description = null,
        Closure|string|null $icon = null,
        Closure|string|null $chevronIcon = null,
        Closure|string|null $color = null,
        Closure|ToolWidget|null $loading = null,
        Closure|Js|string|null $js = null,
        bool $useOutput = false,
    ): MapToolWidget
    {
        if (is_array($center)) {
            $center = fn() => $center;
        }

        if (is_int($zoom)) {
            $zoom = fn() => $zoom;
        }

        if (is_array($markers)) {
            $markers = fn() => $markers;
        }

        if (is_string($label)) {
            $label = fn() => $label;
        }

        if (is_string($description)) {
            $description = fn() => $description;
        }

        if (is_string($icon)) {
            $icon = fn() => $icon;
        }

        if (is_string($chevronIcon)) {
            $chevronIcon = fn() => $chevronIcon;
        }

        if (is_string($color)) {
            $color = fn() => $color;
        }

        if ($loading instanceof ToolWidget) {
            $loading = fn() => $loading;
        }

        if (is_string($js)) {
            $js = fn() => new Js($js);
        }

        if ($js instanceof Js) {
            $js = fn() => $js;
        }

        return app(MapToolWidget::class, [
            'center' => $center,
            'zoom' => $zoom,
            'markers' => $markers,
            'label' => $label,
            'description' => $description,
            'icon' => $icon,
            'chevronIcon' => $chevronIcon,
            'color' => $color,
            'useOutput' => $useOutput,
            'loading' => $loading,
            'js' => $js,
        ]);
    }

    public static function youtube(
        Closure|string|null $url = null,
    ): VideoToolWidget
    {
        return app(VideoToolWidget::class, [
            'url' => $url,
        ]);
    }

    public static function error(
        Closure|string|null $error = null,
        Closure|string|null $details = null,
    ): ErrorToolWidget
    {
        return app(ErrorToolWidget::class, [
            'error' => $error,
            'details' => $details,
        ]);
    }
}
