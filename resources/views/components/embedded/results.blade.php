@props([
    /** @var \App\Livewire\Components\EmbeddedExtractor\StepLabels $labels */
    'labels',

    /** @var \App\Livewire\Components\EmbeddedExtractor\EmbeddingConfig $config */
    'config',
])

<div>
    <x-embedded.header :labels="$labels" />
</div>
<div
    class="flex flex-col gap-5"
    x-data="{ get loading() { return !this.$wire.resultData || this.$wire.resultData === [] || this.$wire.resultData === {} || this.$wire.resultData?.length === 0; } }"
>
    <div>

    </div>

    <x-embedded.buttons :labels="$labels">
        @if ($config->allowDownload && !$config->redirectUrl)
            <x-slot:next-button>
                <x-embedded.download-dropdown />
            </x-slot:next-button>
        @endif
    </x-embedded.buttons>
</div>
