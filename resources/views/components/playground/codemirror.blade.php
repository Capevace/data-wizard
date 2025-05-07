@props([
    'label' => null,
    'statePath' => null,
    'language' => 'json',
    'error' => null,
    'disabled' => false,
])

<div
    {{ $attributes->class('relative grid overflow-hidden') }}
    x-data="{ show: $persist(true).as('show_{{ str($statePath)->slug() }}') }"
    x-bind:id="$id('{{ $statePath }}')"
>
    <x-filament::fieldset
        :$label
        @class([
            '!p-0 bg-white dark:bg-gray-800 w-full !min-w-0',
            'border !border-danger-400 shadow-lg shadow-danger-500/10' => $error,
        ])
    >
        <div
            x-data="JsonEditorComponent({
                statePath: '{{ $statePath }}',
                state: @entangle($statePath),
                language: '{{ $language }}',
                disabled: {{ $disabled ? 'true' : 'false' }},
            })"
            wire:ignore
        >
            <div x-ref="editor" x-show="show"></div>
            <div x-show="!show" class="px-4 py-2 flex items-center justify-center gap-3">
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Hidden</p>
                <x-filament::button
                    size="xs"
                    color="gray"
                    icon="heroicon-o-eye"
                    icon-position="after"
                    x-on:click="show = !show"
                >
                    Show
                </x-filament::button>
            </div>
        </div>
    </x-filament::fieldset>

    @if ($error)
        <p class="text-danger-800/75 dark:text-danger-400 text-xs mt-2 font-medium">
            {{ $error }}
        </p>
    @endif

    <x-filament::icon-button
        class="!absolute top-0 right-0 mt-5 mr-0"
        icon="heroicon-o-eye-slash"
        size="lg"
        @click="show = !show"
        x-show="show"
    />
</div>
