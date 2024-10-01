<x-filament-panels::page>
    @if ($record = $this->getRecord())
        <livewire:components.generator
            :extractor-id="$record->saved_extractor?->id"
            :bucket-id="$record->bucket?->id"
            :run-id="$record->id"
            :debug-mode-enabled="$this->debugModeEnabled"
        />
    @else
        <div class="flex flex-col gap-4">
            No record found
        </div>
    @endif
</x-filament-panels::page>
