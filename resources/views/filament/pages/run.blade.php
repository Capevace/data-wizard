<x-filament-panels::page>
    <livewire:components.generator
        :extractor-id="$this->getRecord()->saved_extractor->id"
        :bucket-id="$this->getRecord()->bucket->id"
        :run-id="$this->getRecord()->id"
        :debug-mode-enabled="$this->debugModeEnabled"
    />
</x-filament-panels::page>
