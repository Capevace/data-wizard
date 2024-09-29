@props([
    /** @var \App\Livewire\Components\EmbeddedExtractor\StepLabels $labels */
    'labels',
])

<x-embedded.heading @class([
    'mb-4' => $labels->description === null,
    'mb-1' => $labels->description !== null,
])>
    {{ $labels->heading ?? 'Welcome' }}
</x-embedded.heading>

@if ($labels->description)
    <div class="prose text-gray-500 dark:text-gray-300 mb-4">
        {!! $labels->description !!}
    </div>
@endif
