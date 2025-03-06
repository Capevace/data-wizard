<x-filament::dropdown {{ $attributes }}>
    <x-slot:trigger>
        <x-filament::button
            icon="bi-cloud-download"
            class="w-full"
        >
            {{ __('Download data') }}
        </x-filament::button>
    </x-slot:trigger>

    <x-filament::dropdown.list>
        {{ $this->downloadAsCsv }}
        {{ $this->downloadAsJson }}
        {{ $this->downloadAsXml }}
    </x-filament::dropdown.list>
</x-filament::dropdown>
