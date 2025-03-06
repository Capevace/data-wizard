@props([
    /** @var string $heading */
    'heading',

    /** @var ?string $resources Slot to add more resources */
    'resources' => null,

    'color' => 'sky',
])

<article {{ $attributes->class("border border-sdashed border-{$color}-700 bg-gradient-to-br from-{$color}-800/50 to-{$color}-900/50 rounded px-5 py-3") }}>
    <header class="border-b border-{{ $color }}-700 mb-4 flex items-center justify-between">
        <h3 class="text-lg">{{ $heading }}</h3>

        <button>
            @svg('heroicon-o-lock-open', 'w-5 h-5 text-orange-400')
        </button>
    </header>

    {{ $slot }}

    @if ($resources)
        <x-llm-magic::resource.sub-resources :label="$heading">
            {{ $resources }}
        </x-llm-magic::resource.sub-resources>
    @endif
</article>
