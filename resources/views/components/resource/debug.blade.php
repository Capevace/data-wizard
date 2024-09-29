@props([
    'label' => null,
    'bindLabel' => null,
    'bindValue' => null,
    'xModel' => '$wire.resultData',
    'editable' => true,
    'always' => false
])

<?php
$value = $bindValue ?? $xModel;
?>

<template
    x-if="{{ $always ? 'true' : 'false' }} || $store.debug || debugModeEnabled"
>
    <details class="cursor-pointer px-3 rounded border !border-gray-400/30 border-gray-400/50 dark:border-gray-700 bg-gradient-to-br from-gray-50/80 to-gray-200/80 dark:from-gray-800/50 dark:to-gray-900/50 shadow-sm">
        <summary class="text-gray-700 dark:text-gray-200">JSON</summary>
        <pre
            {{ $attributes->class("prettyjson font-mono overflow-x-auto p-0.5")->style('font-size: 0.5rem') }}
            x-data="{
                get json() {
                    return window.prettyPrint(this.{!! $value !!});
                }
            }"
            x-html="json"
        ></pre>
    </details>
</template>
