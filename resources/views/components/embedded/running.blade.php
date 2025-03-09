@props([
    /** @var \App\Livewire\Components\EmbeddedExtractor\StepLabels $labels */
    'labels',

    'canContinue',
])

<div>
    <x-embedded.header :labels="$labels" />
</div>
<div
    class="flex flex-col gap-5"
    x-data="{ get loading() { return !this.$wire.resultData || this.$wire.resultData === [] || this.$wire.resultData === {} || this.$wire.resultData?.length === 0; } }"
>
    <div
        x-show="loading"
        class="flex items-center justify-center flex-col py-10"

        @if ($this->run?->partial_data !== null && count($this->run->partial_data) === 0)
            x-cloak
        @endif
    >
        <x-filament::loading-indicator class="w-16 h-16 text-gray-500" wire:ignore />
    </div>

    <div
        x-show="!loading"
        class="flex flex-col gap-5"

        @if ($this->run?->partial_data === null || count($this->run->partial_data) === 0)
            x-cloak
        @endif
    >
        <x-llm-magic::resource.json-schema
            :schema="$this->saved_extractor->json_schema"
            :disabled="$this->run?->status->shouldPoll()"
        />

        <x-llm-magic::resource.debug />
    </div>

    <x-embedded.buttons :labels="$labels" :$canContinue />
</div>
