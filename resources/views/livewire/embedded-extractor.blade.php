<?php

use App\Livewire\Components\EmbeddedExtractor\ExtractorSteps;
use App\Livewire\Components\EmbeddedExtractor\WidgetAlignment;

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
                <img
                    src="{{ $this->config->logoUrl }}"
                    alt="Logo BBK"
                    class="h-32"
                >
            </div>
        @endif

        <div
            @class([
                $this->config->backgroundClasses,
                $this->config->borderClasses,
                $this->config->borderRadiusClasses,
                $this->config->paddingClasses,
                $this->config->shadowClasses,
                'max-w-3xl' => $this->horizontalAlignment === WidgetAlignment::Center,
                'flex-1' => $this->config->verticalAlignment === WidgetAlignment::Stretch,
            ])
        >
            @if ($this->step === ExtractorSteps::Introduction)
                <x-embedded.introduction :labels="$this->introduction_labels" />
            @elseif ($this->step === ExtractorSteps::Bucket)
                <x-embedded.bucket :labels="$this->bucket_labels" />
            @elseif ($this->step === ExtractorSteps::Extraction)
                <x-embedded.running
                    :labels="$this->extraction_labels"
                    :can-continue="$this->canContinue()"
                    :limit-height="$this->verticalAlignment === WidgetAlignment::Center"
                />
            @elseif ($this->step === ExtractorSteps::Results)
                <x-embedded.results
                    :labels="$this->results_labels"
                    :config="$this->config"
                />
            @endif
        </div>
    </div>

    <x-filament-actions::modals />
</div>
