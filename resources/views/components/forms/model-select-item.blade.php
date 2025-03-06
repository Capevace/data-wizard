@props(['key', 'label', 'icon'])

<span class="flex items-center gap-2 overflow-visible">
    <x-icon :name="$icon" class="flex-shrink-0 w-4 h-4 text-gray-500 dark:text-gray-400" />
    <span class="text-xs truncate flex-1">
        {{ $label }}
    </span>
</span>
