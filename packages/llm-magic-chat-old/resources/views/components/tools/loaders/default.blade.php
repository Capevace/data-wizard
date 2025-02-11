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

<div class="flex items-center justify-start w-full gap-5 py-2 mb-5">
    <x-dynamic-component
        :component="$output ? $doneIcon : $loadingIcon"
        @class([
            'w-8 h-8',
            match ($output ? $doneIconColor : $loadingIconColor) {
                'primary' => 'text-primary-600',
                'warning' => 'text-warning-600',
                'success' => 'text-success-600',
                'danger' => 'text-danger-600',
                'info' => 'text-info-600',
                default => 'text-gray-600',
            }
        ])
    />

    <div class="font-semibold text-gray-300">{{ $content }}</div>
</div>
