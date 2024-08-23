<x-filament-panels::page>
    <livewire:components.generator
        :bucket-id="$this->getRecord()->bucket->id"
        :run-id="$this->getRecord()->id"
    />
</x-filament-panels::page>
