@props([
    /** @var string $label */
    'label',

    /** @var ?string $value */
    'value' => null
])

<dl {{ $attributes->class([
    'col-span-3' => str($value)->wordCount() > 5,
]) }}>
    <dt class="text-xs text-sky-300">{{ $label }}:</dt>
    <dd class="text-lg">{{ $value ?? $slot }}</dd>
</dl>
