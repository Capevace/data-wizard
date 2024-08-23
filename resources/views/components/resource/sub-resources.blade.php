@props([
    /** @var string $label */
    'label',

    'containerClass' => null
])

<section {{ $attributes }}>
    <h3 class="text-xs text-sky-400 mt-6">{{ $label }}</h3>
    <div class="border-t border-sky-700 pt-3 mt-1 {{ $containerClass }}">
        {{ $slot }}
    </div>
</section>
