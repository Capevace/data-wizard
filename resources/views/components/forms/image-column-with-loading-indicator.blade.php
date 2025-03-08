@props([
    'src' => null,
    'getState' => fn () => null,
    'getExtraAttributes' => fn () => [],
    'isInline' => fn () => false,
])
@php
    $url = $src ?? $getState();
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-ta-image',
                'px-3 py-4' => ! $isInline(),
            ])
            ->style([
                "aspect-ratio: 4/3",
                "width: 10rem",
            ])
    }}
    x-data="{
        url: null,
        init() {
            this.checkImage('{{ $url }}');
        },

        checkImage(url, timeout = 1000) {
            const img = new Image();
            img.src = url;
            img.onload = () => {
                this.width = img.width;
                this.height = img.height;
                this.url = url;
            };

            img.onerror = () => {
                setTimeout(() => {
                    this.checkImage(url, timeout * 1.5);
                }, timeout);
            };
        },
    }"
>
    <img
        x-show="url"
        class="w-full h-full object-cover object-center rounded"
        style="aspect-ratio: 4/3"
        x-bind:src="url"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    />

    <div
        x-show="!url"
        class="w-full h-full flex items-center justify-center"
        style="aspect-ratio: 4/3"
    >
        <x-filament::loading-indicator  class="w-10 h-10" />
    </div>
</div>
