@props([
    /** @var InvokableFunction $tool */
    'tool',

    /** @var \Mateffy\Magic\LLM\Message\FunctionInvocationMessage $invocation */
    'invocation',

    /** @var ?\Mateffy\Magic\LLM\Message\FunctionOutputMessage $output */
    'output' => null,
])

@if (!$output)
    <form class="w-full py-1 mb-5" x-data="{ data: {} }" wire:submit.prevent="sendForm(data)">
        <div class="grid grid-cols-1 gap-2" >
            <x-resource.json-schema :schema="$schema" state-path="data" />
        </div>

        <nav class="flex justify-end py-3">
            <x-filament::button
                type="submit"
                color="primary"
            >
                Submit
            </x-filament::button>
        </nav>
    </form>
@else
    <x-filament-tables::empty-state
        heading="Success"
        description="The tool was successful."
        icon="heroicon-o-check-circle"
        color="success"
    />
@endif
