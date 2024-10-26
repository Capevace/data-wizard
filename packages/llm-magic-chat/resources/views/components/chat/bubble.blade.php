@props([
    'rounding' => 'rounded-lg',
    'textSize' => 'text-sm',
    'textColor' => 'text-gray-800 dark:text-gray-200',
    'backgroundColor' => 'bg-gray-50 dark:bg-gray-800',
    'padding' => 'px-5 py-3',
    'shadow' => 'shadow-sm',
])

<div {{ $attributes->class(['relative', $padding, $shadow, $rounding, $textSize, $textColor, $backgroundColor]) }}>{{ $slot }}</div>
