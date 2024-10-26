@props([
    /** @var InvokableFunction $tool */
    'tool',

    /** @var \Mateffy\Magic\LLM\Message\FunctionInvocationMessage $invocation */
    'invocation',

    /** @var ?\Mateffy\Magic\LLM\Message\FunctionOutputMessage $output */
    'output' => null,
])

@if (!$output)
    <form class="w-full py-1 mb-5" x-data="{ data: @js($invocation->call->arguments) }" wire:submit.prevent="continueAfterInterrupt(data)">
        <div class="grid grid-cols-1 gap-2" >
            <x-resource.json-schema
                :schema="$tool->schema()"
                state-path="data"
            />
        </div>

        <nav class="flex justify-end py-3 gap-5">
            <x-filament::link
                icon="heroicon-o-x-mark"
                color="danger"
                wire:click="cancelAfterInterrupt"
            >
                Cancel
            </x-filament::link>
            <x-filament::button
                type="submit"
                icon="bi-check"
                icon-position="after"
                color="success"
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
