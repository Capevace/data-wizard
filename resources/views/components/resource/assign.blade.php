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
//$altKey =
?>


<pre>{{ $name }} {{ $statePath }}</pre>
@if ($cloak)
<template x-if="$wire.{{ $key }}">
@endif

<{{ $tag }}
    {{ $attributes
        ->class('')
    }}
    x-data="{ '{{ $name }}': '{{ $statePath }}' }"
>
    {{ $slot }}
</{{ $tag }}>

@if ($cloak)
</template>
@endif
