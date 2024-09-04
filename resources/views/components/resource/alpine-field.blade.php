@props([
    'label' => null,
    'bindLabel' => null,
    'bindValue' => null,
    'xModel' => null,
    'editable' => true,
])

<?php
$value = $bindValue ?? $xModel;
?>

<dl
    {{ $attributes->class('mt-2') }}
    :class="{
        'col-span-2': ({!! $value !!}).length > 25,
    }"
>
    <dt class="text-xs text-sky-300" @if ($bindLabel) x-text="{{ $bindLabel }}" @endif>{{ $label }}</dt>
    <dd
        {{
            $attributes
                ->merge($editable ? [
                    'contenteditable' => 'true',
                    'x-on:input' => "{$xModel} = \$event.target.innerText",
                ]: [])
        }}

        x-text="{!! $value !!}"
        :class="{
            'text-lg': ({!! $value !!}).length <= 25,
            'text-sm': ({!! $value !!}).length > 35
        }"
    ></dd>
</dl>
