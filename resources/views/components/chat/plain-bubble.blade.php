@props([
    'rounding' => 'rounded-lg',
    'textSize' => 'text-sm/6',
    'textColor' => 'text-gray-900 dark:text-gray-50',
    'backgroundColor' => 'br-transparent',
    'padding' => '',
    'shadow' => '',
])

<div {{ $attributes->class(['relative', $padding, $shadow, $rounding, $textSize, $textColor, $backgroundColor]) }}>{{ $slot }}</div>
