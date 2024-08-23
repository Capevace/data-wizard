@props([
    'label' => null,
    'bindLabel' => null,
    'bindValue' => null
])

<dl
    {{ $attributes->class('mt-2') }}
    :class="{
        'col-span-2': {{ $bindValue }}.length > 25,
    }"
>
    <dt class="text-xs text-sky-300" @if ($bindLabel) x-text="{{ $bindLabel }}" @endif>{{ $label }}</dt>
    <dd
        x-text="{{ $bindValue }}"
        :class="{
            'text-lg': {{ $bindValue }}.length <= 25,
            'text-sm': {{ $bindValue }}.length > 35
        }"
    ></dd>
</dl>
