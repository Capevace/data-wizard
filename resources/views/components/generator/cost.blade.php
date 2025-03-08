@props([
    /** @var \Mateffy\Magic\Chat\TokenStats $stats */
    'stats',
])

<?php
$inputCostInCents = $stats->cost?->inputCostInCents($stats->inputTokens);
$outputCostInCents = $stats->cost?->outputCostInCents($stats->outputTokens);
$totalCostInCents = $inputCostInCents + $outputCostInCents;
?>

<div {{ $attributes->class('grid grid-cols-2 gap-2') }}>
    <x-generator.key-value icon="heroicon-o-currency-euro" :label="__('Tokens')">
        <span class="text-lg">{{ $stats->tokens }}</span>
    </x-generator.key-value>

    <x-generator.key-value icon="heroicon-o-currency-euro" :label="__('Total Cost')">
        <span class="text-lg">{{ $stats->calculateTotalCost()?->format() ?? '–' }}</span>
    </x-generator.key-value>

    <x-generator.key-value icon="heroicon-o-currency-euro" :label="__('Input Tokens')">
        <span class="text-lg">{{ $stats->inputTokens }}</span>
    </x-generator.key-value>

    <x-generator.key-value icon="heroicon-o-currency-euro" :label="__('Input Cost')">
        <span class="text-lg">{{ $stats->calculateInputCost()?->format() ?? '–' }}</span>
        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __(':value / 1M Tokens', ['value' => $stats->cost?->inputPricePer1M()->format()]) }}</span>
    </x-generator.key-value>

    <x-generator.key-value icon="heroicon-o-currency-euro" :label="__('Output Tokens')">
        <span class="text-lg">{{ $stats->outputTokens }}</span>
    </x-generator.key-value>

    <x-generator.key-value icon="heroicon-o-currency-euro" :label="__('Output Cost')">
        <span class="text-lg">{{ $stats->calculateOutputCost()?->format() ?? '–' }}</span>
        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __(':value / 1M Tokens', ['value' => $stats->cost?->outputPricePer1M()->format()]) }}</span>
    </x-generator.key-value>

</div>
