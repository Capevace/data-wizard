@props([
    /** @var \App\Livewire\Components\EmbeddedExtractor\StepLabels $labels */
    'labels',
])

<div class="flex flex-col gap-5">
    <x-embedded.header :labels="$labels" />
    <x-embedded.buttons :labels="$labels" />
</div>
