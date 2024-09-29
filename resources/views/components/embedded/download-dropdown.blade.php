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
        <x-filament::dropdown.list.item
            icon="bi-filetype-csv"
        >
            CSV
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item
            icon="bi-filetype-json"
        >
            JSON
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item
            icon="bi-filetype-xml"
        >
            XML
        </x-filament::dropdown.list.item>
        <x-filament::dropdown.list.item
            icon="bi-filetype-xlsx"
        >
            Excel (.xlsx)
        </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
