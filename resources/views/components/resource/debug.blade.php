@props([
    'label' => null,
    'bindLabel' => null,
    'bindValue' => null,
    'xModel' => null,
    'editable' => true,
    'always' => false
])

<?php
$value = $bindValue ?? $xModel;
?>

<template
    x-if="{{ $always ? 'true' : 'false' }} || $store.debug || debugModeEnabled"
>
    <pre
        {{ $attributes->class('font-mono overflow-x-auto rounded p-0.5 border shadow-inner')->style('font-size: 0.5rem') }}
        x-text="JSON.stringify({!! $value !!}, null, 2)"
    ></pre>
</template>
