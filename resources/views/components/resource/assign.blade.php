@props([
    /** @var ?string $statePath */
    'statePath' => null,

    /** @var string $name */
    'name',

    'tag' => 'div',

    'cloak' => true,
])

<?php
$key = 'partialResultJson' . ($statePath ? "['{$statePath}']" : '');
?>


@if ($cloak)
<template x-if="$wire.{{ $key }}">
@endif

<{{ $tag }}
    {{ $attributes
        ->class('')
    }}
    x-data="{ '{{ $name }}': @entangle($key) }"
>
    {{ $slot }}
</{{ $tag }}>

@if ($cloak)
</template>
@endif
