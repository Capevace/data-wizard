@props([
    /** @var string $statePath */
    'statePath',

    /** @var string $name */
    'name',
])

<template x-for="({{ $name }}, {{ $name }}_index) in $wire.partialResultJson?.{{ $statePath }}" :key="{{ $name }}_index">
    {{ $slot }}
</template>
