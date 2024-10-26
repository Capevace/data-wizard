@props([
    /** @var InvokableFunction $tool */
    'tool',

    /** @var \Mateffy\Magic\LLM\Message\FunctionInvocationMessage $invocation */
    'invocation',

    /** @var ?\Mateffy\Magic\LLM\Message\FunctionOutputMessage $output */
    'output' => null,

    'content',

    /** @var string $loadingIcon */
    'loadingIcon',

    /** @var 'primary'|'warning'|'success'|'danger'|'info'|'gray' $loadingIconColor */
    'loadingIconColor',

    /** @var string $doneIcon */
    'doneIcon',

    /** @var 'primary'|'warning'|'success'|'danger'|'info'|'gray' $doneIconColor */
    'doneIconColor',
])

<x-llm-magic::tools.dropdown
    :icon="$output ? $doneIcon : $loadingIcon"
    :color="$output ? $doneIconColor : $loadingIconColor"
    :label="$content"
    :description="str($tool->name())->snake()->replace('_', ' ')->title()"
>
    <pre
        {{ $attributes->class("prettyjson font-mono overflow-x-auto p-0.5")->style('font-size: 0.5rem') }}
        x-data="{
            output: @js($output?->output),
            debugModeEnabled: false,

            get json() {
                return window.prettyPrint(this.output);
            }
        }"
        x-html="json"
    ></pre>
</x-tools.dropdown>
