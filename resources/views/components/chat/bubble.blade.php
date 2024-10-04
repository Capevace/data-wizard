@props([
    'rounding' => 'rounded-lg',
    'textSize' => 'text-sm',
    'textColor' => 'text-gray-800 dark:text-gray-200',
    'backgroundColor' => 'bg-gray-50 dark:bg-gray-800',
])

<div {{ $attributes->class(['relative px-5 py-3 shadow-sm', $rounding, $textSize, $textColor, $backgroundColor]) }}>{{ $slot }}</div>
