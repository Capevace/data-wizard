@props([
    /** @var \App\Livewire\Components\EmbeddedExtractor\StepLabels $labels */
    'labels',

    'nextButton' => null,
    'backButton' => null,
    'secondaryButton' => null,
    'canContinue' => true,
])

<nav @class([
    'w-full gap-5 items-center justify-end ',
    'grid grid-cols-2' => $labels->secondaryButton !== null || $labels->backButton !== null || $secondaryButton !== null || $backButton !== null,
    'flex' => $labels->secondaryButton === null && $labels->backButton === null && $backButton === null && $secondaryButton === null,
])>
    @if ($labels->secondaryButton || $secondaryButton)
        <div class="col-span-1 flex items-center gap-5">
    @endif

    @if ($backButton)
        {{ $backButton }}
    @elseif ($labels->backButton)
        <x-filament::button
            :color="$labels->backButton->color"
            :icon="$labels->backButton->icon"
            :wire:click="$labels->backButton->action"
            icon-position="before"
            class="flex-1 {{ $labels->secondaryButton || $secondaryButton ? 'col-span-1' : '' }}"
            :disabled="$labels->backButton->disabledWhileRunning && $this->run?->status->shouldPoll()"
            data-pan="embedded-extractor-back-{{ $labels->backButton->action }}"
        >
            {{ $labels->backButton->label }}
        </x-filament::button>
    @endif

    @if ($secondaryButton)
        {{ $secondaryButton }}
    @elseif ($labels->secondaryButton)
        <x-filament::button
            :color="$labels->secondaryButton->color"
            :icon="$labels->secondaryButton->icon"
            :wire:click="$labels->secondaryButton->action"
            icon-position="before"
            class="flex-1"
            :disabled="$labels->secondaryButton->disabledWhileRunning && $this->run?->status->shouldPoll()"
            data-pan="embedded-extractor-secondary-{{ $labels->secondaryButton->action }}"
        >
            {{ $labels->secondaryButton->label }}
        </x-filament::button>
    @endif

    @if ($labels->secondaryButton || $secondaryButton)
        </div>
    @endif

    @if ($nextButton)
        {{ $nextButton }}
    @elseif ($labels->nextButton)
        <x-filament::button
            @class([
                'flex-1 col-span-1',
                'opacity-50 cursor-not-allowed' => !$canContinue,
            ])
            :color="$labels->nextButton->color"
            :icon="$labels->nextButton->icon"
            :wire:click="$canContinue ? $labels->nextButton->action : null"
            icon-position="after"
            :disabled="$labels->nextButton->disabledWhileRunning && $this->run?->status->shouldPoll()"
            data-pan="embedded-extractor-next-{{ $labels->nextButton->action }}"
            :tooltip="$canContinue ? null : __('Fix all validation errors before continuing.')"
            :aria-disabled="!$canContinue"
        >
            {{ $labels->nextButton->label }}
        </x-filament::button>
    @endif
</nav>
