<div class="-mx-2 -mt-2 mft-sidebar-search">
    @livewire(\Filament\Livewire\GlobalSearch::class)
</div>

<div class="mft-sidebar-search-icon flex justify-center w-full">
    <x-filament::icon-button
        icon="heroicon-o-magnifying-glass"
        color="gray"
        @click="
            $store.sidebar.open();

            $nextTick(() => {
                document.querySelector('.mft-sidebar-search input').focus();
            });
        "
    />
</div>
