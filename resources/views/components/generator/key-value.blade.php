@props([
    'icon' => 'heroicon-o-clock',
    'label',
])

<div {{ $attributes->class('flex flex-col gap-1.5') }}>
    <div class="flex items-center gap-2 dark:text-gray-400">
        @svg($icon, 'w-5 h-5')
        <h3 class="text-sm">{{ $label }}</h3>
    </div>

    {{ $slot }}
</div>
