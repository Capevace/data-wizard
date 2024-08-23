<x-filament-panels::page>
    <div x-data="{ search: '' }">
        <form class="mb-5">
            <x-filament::input.wrapper
                inline-prefix
                prefix-icon="heroicon-m-magnifying-glass"
                prefix-icon-alias="tables::search-field"
            >
                <x-filament::input
                    autofocus
                    x-init="$el.focus()"
                    x-model="search"
                    type="search"
                    placeholder="{{ __('Search...') }}"
                    inline-prefix
                    autocomplete="off"
                />
            </x-filament::input.wrapper>
        </form>

        {{ $this->infolist }}
    </div>
</x-filament-panels::page>
