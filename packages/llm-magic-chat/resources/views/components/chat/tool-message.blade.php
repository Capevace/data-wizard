@props([
    /** @var \Mateffy\Magic\Functions\InvokableFunction $tool */
    'tool',

    /** @var \Mateffy\Magic\LLM\Message\FunctionInvocationMessage $message */
    'invocation',

    /** @var \Mateffy\Magic\LLM\Message\FunctionOutputMessage|null $output */
    'output' => null,

    'streaming' => false,
    'isSecondLast' => false,
    'isCurrent' => false,
])

<x-llm-magic::chat.bubble
    @class([
        'flex-1 flex items-center gap-3 font-semibold rounded-lg mb-5',
        'border-b border-gray-200 dark:border-gray-700' => !$output,
    ])
>
    <x-icon name="bi-robot" class="w-5 h-5 text-green-500" />
    <span class="flex-1">{{ str($invocation->call->name)->snake()->replace('_', ' ')->title() }}</span>

    @if ($streaming && $isCurrent)
        <x-filament::loading-indicator class="w-5 h-5" />
    @endif

    <pre
        class="w-full bg-transparent text-inherit {{ is_array($output->output) ? '[&>.json-value]:!text-primary-400 [&>.json-string]:!text-primary-400 [&>.json-key]:!text-primary-100 dark:[&>.json-key]:!text-primary-800' : '' }}"
        style="font-size: .55rem"
        x-html="window.prettyPrint({{ is_array($output->output) ? json_encode($output->output, JSON_PRETTY_PRINT) : $output->output }})"
    ></pre>
</x-llm-magic::chat.bubble>
