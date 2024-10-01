<?php
use App\WidgetAlignment;
use App\Livewire\Components\EmbeddedExtractor\ExtractorSteps;

$horizontalClasses = match ($this->config->horizontalAlignment) {
    WidgetAlignment::Start => 'items-start',
    WidgetAlignment::Center => 'items-center',
    WidgetAlignment::End => 'items-end',
    WidgetAlignment::Stretch => 'items-stretch',
};

$verticalClasses = match ($this->config->verticalAlignment) {
    WidgetAlignment::Start => 'justify-start',
    WidgetAlignment::Center => 'justify-center',
    WidgetAlignment::End => 'justify-end',
    WidgetAlignment::Stretch => 'justify-stretch',
};
?>

@assets
@php
    \Filament\Support\Facades\FilamentColor::register([
        'primary' => \Filament\Support\Colors\Color::Neutral,
        'warning' => \Filament\Support\Colors\Color::Yellow,
        'success' => \Filament\Support\Colors\Color::Emerald,
        'gray' => \Filament\Support\Colors\Color::Neutral,
    ]);
@endphp

{{ filament()->getPanel('app')->getTheme()->getHtml() }}
{{ filament()->getPanel('app')->getFontHtml() }}

<style>
    :root {
        --font-family: '{!! filament()->getFontFamily() !!}';
        --sidebar-width: {{ filament()->getSidebarWidth() }};
        --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
        --default-theme-mode: {{ filament()->getDefaultThemeMode()->value }};
    }
</style>

{{--    <script src="//cdn.tailwindcss.com"></script>--}}
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {

            }
        }
    }
</script>

<script>
    const theme = localStorage.getItem('theme') ?? @js(filament()->getDefaultThemeMode()->value)

    if (
        theme === 'dark' ||
        (theme === 'system' &&
            window.matchMedia('(prefers-color-scheme: dark)')
                .matches)
    ) {
        document.documentElement.classList.add('dark')
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>

@vite([
    'resources/css/filament/app/theme.css',
    'resources/css/app.css',
])

{!! \Filament\Support\Facades\FilamentAsset::renderStyles() !!}

@endassets


<div
    @class([
        $this->config->outerPaddingClasses,
        'min-h-screen flex flex-col',
        $horizontalClasses,
        $verticalClasses,
    ])

    @if ($this->run?->status->shouldPoll())
        wire:poll.300ms="refreshJson"
    @endif

    x-data="{
        debugModeEnabled: true,

        init() {
            this.$wire.refreshJson();

            // ResizeObserver for card height
            this.resizeObserver = new ResizeObserver(() => {
                window.parent.postMessage({ type: 'magic-iframe-resize', height: this.$refs.card.offsetHeight }, '*');
            });
            this.resizeObserver.observe(this.$refs.card);
        },
    }"

    @magic-iframe-submit.window="
        window.parent.postMessage({ type: 'magic-iframe-submit', data: $event.detail.data }, '*');
    "
>
{{--    <style>--}}
{{--        body {--}}
{{--            background-color: {{ $bodyBackgroundColor }};--}}
{{--        }--}}

{{--        body.dark {--}}
{{--            background-color: {{ $bodyBackgroundColorDark }};--}}
{{--        }--}}
{{--    </style>--}}

    <div
        x-ref="card"
        @class([
            'magic-card flex flex-col',
            'w-full' => $this->config->maxWidth || $this->config->horizontalAlignment === WidgetAlignment::Stretch,
            'w-fit sm:min-w-[30rem]' => $this->config->maxWidth === null && $this->config->horizontalAlignment !== WidgetAlignment::Stretch,
            'flex-1' => $this->config->verticalAlignment === WidgetAlignment::Stretch,
        ])
        @style([
            "max-width: {$this->config->maxWidth}" => $this->config->maxWidth && $this->config->horizontalAlignment !== WidgetAlignment::Stretch,
        ])
    >
        @if ($this->config->logoUrl)
            <div
                @class([
                    'flex items-center w-full',
                    match ($this->config->logoAlignment) {
                        WidgetAlignment::Start => 'justify-start',
                        WidgetAlignment::Center => 'justify-center',
                        WidgetAlignment::End => 'justify-end',
                        WidgetAlignment::Stretch => 'justify-stretch',
                    },
                ])
            >
                <img src="{{ $this->config->logoUrl }}" alt="Logo BBK" class="h-32">
            </div>
        @endif

        <div
            @class([
                $this->config->backgroundClasses,
                $this->config->borderClasses,
                $this->config->borderRadiusClasses,
                $this->config->paddingClasses,
                $this->config->shadowClasses,
                'flex-1' => $this->config->verticalAlignment === WidgetAlignment::Stretch,
            ])
        >
            @if ($this->step === ExtractorSteps::Introduction)
                <x-embedded.introduction :labels="$this->introduction_labels" />
            @elseif ($this->step === ExtractorSteps::Bucket)
                <x-embedded.bucket :labels="$this->bucket_labels" />
            @elseif ($this->step === ExtractorSteps::Extraction)
                <x-embedded.running :labels="$this->extraction_labels" />
            @elseif ($this->step === ExtractorSteps::Results)
                <x-embedded.results :labels="$this->results_labels" :config="$this->config" />
            @endif
        </div>
    </div>

    <x-filament-actions::modals />
</div>
