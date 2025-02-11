@props([
    'label',
    'description' => null,

    /** @var ?string $icon */
    'icon' => null,
    'chevronIcon' => 'heroicon-o-chevron-down',

    'color' => 'gray',

    'trigger' => null,

    'collapsed' => true
])

<div
    {{ $attributes->class('relative overflow-hidden w-full mb-5 group cursor-pointer flex flex-col-reverse') }}
    x-data="{ collapsed: @js($collapsed) }"
>
    <div
        x-show="!collapsed"
        class="absolute left-0 top-0 right-0 h-64 bg-gradient-to-b from-gray-50 dark:from-gray-800/50 to-transparent pointer-events-none"
        x-transition:enter="animate-fade-down animate-alternate animate-duration-300 duration-300 "
        x-transition:leave="animate-fade-down animate-alternate-reverse animate-duration-200 duration-200 "
        :class="{
            'opacity-100': !collapsed,
            'opacity-0': collapsed,
        }"
    ></div>

    <div
        class="relative w-full transform transition px-5 py-3"
        x-transition:enter="animate-fade-down animate-alternate animate-duration-300 duration-300 "
        x-transition:leave="animate-fade-down animate-alternate-reverse animate-duration-200 duration-200 "
        :class="{
            'opacity-100': !collapsed,
            'opacity-0 pointer-events-none': collapsed,
        }"
        x-show="!collapsed"
    >
        {{ $slot }}
    </div>

    <div @click="collapsed = !collapsed">
        @if ($trigger)
            {{ $trigger }}
        @else
            <button
                type="button"
                @class([
                    'relative rounded-lg text-left flex items-center justify-start w-full gap-5 py-2 transition-colors duration-250 group-hover:bg-gray-50 group-hover:dark:bg-gray-700/50 px-5',
                ])
            >
                @if (str($icon)->startsWith('heroicon-o-'))
                    <x-icon
                        :name="$icon"
                        @class([
                            'relative w-8 h-8 flex-shrink-0',
                            match ($color) {
                                'primary' => 'text-primary-600',
                                'warning' => 'text-warning-600',
                                'success' => 'text-success-600',
                                'danger' => 'text-danger-600',
                                'info' => 'text-info-600',
                                default => 'text-gray-600',
                            }
                        ])
                    />
                @elseif ($icon)
                    <x-dynamic-component
                        :component="$icon"
                        @class([
                            'relative w-8 h-8 flex-shrink-0',
                            match ($color) {
                                'primary' => 'text-primary-600',
                                'warning' => 'text-warning-600',
                                'success' => 'text-success-600',
                                'danger' => 'text-danger-600',
                                'info' => 'text-info-600',
                                default => 'text-gray-600',
                            }
                        ])
                    />
                @endif

                <div class="relative flex-1 flex flex-col gap-1">
                    <p class="font-semibold">{{ $label }}</p>

                    @if ($description)
                        <p class="text-sm font-medium dark:text-gray-300 text-gray-400">{{ $description }}</p>
                    @endif
                </div>

                <div
                    class="relative group-hover:opacity-100 transition-opacity duration-250"
                    :class="{ 'opacity-0': collapsed, 'opacity-100': !collapsed }"
                >
                    <x-icon
                        :name="$chevronIcon"
                        class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform duration-250"
                        x-bind:class="{ 'rotate-180': !collapsed }"
                    />
                </div>
            </button>
        @endif
    </div>
</div>
