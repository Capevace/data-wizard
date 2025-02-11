<x-filament-panels::page class="fi-dashboard-page">
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />

    <div class="grid md:grid-cols-2 gap-8">
        <x-filament::section
            icon="bi-robot"
            heading="{{ __('Define your datamodel using an extractor') }}"
            description="{{ __('To extract structured data from your source material, define a JSON schema that describes the shape of the data you want to receive.') }}"
        >
            <nav class="grid grid-cols-2 items-start w-full gap-5">
                <div class="flex flex-col gap-2">
                    <x-filament::button
                        icon="bi-upload"
                        icon-position="after"
                        color="gray"
                        tag="a"
                        href="{{ \App\Filament\Resources\SavedExtractorResource\Pages\ListSavedExtractors::getUrl() }}"
                    >
                        {{ __('Import an extractor') }}
                    </x-filament::button>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('You can use your previous extractors or use ones you got from the community.') }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <x-filament::button
                        icon="heroicon-o-plus"
                        icon-position="after"
                        tag="a"
                        href="{{ \App\Filament\Resources\SavedExtractorResource\Pages\CreationWizard::getUrl() }}"
                    >
                        {{ __('Create a new extractor') }}
                    </x-filament::button>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('You can use your previous extractors or use ones you got from the community.') }}
                    </p>
                </div>
            </nav>
        </x-filament::section>

        <x-filament::section
            icon="bi-upload"
            heading="{{ __('Upload a dataset') }}"
            description="{{ __('If you have some files you want to keep running extractions on, upload them into a reusable bucket.') }}"
        >
            <nav class="grid grid-cols-2 items-start w-full gap-5">
                <div class="flex flex-col gap-2">
                    <x-filament::button
                        icon="bi-upload"
                        icon-position="after"
                        color="gray"
                        tag="a"
                        href="{{ \App\Filament\Resources\SavedExtractorResource\Pages\ListSavedExtractors::getUrl() }}"
                    >
                        {{ __('Import an extractor') }}
                    </x-filament::button>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('You can use your previous extractors or use ones you got from the community.') }}
                    </p>
                </div>
                <div class="flex flex-col gap-2">
                    <x-filament::button
                        icon="heroicon-o-plus"
                        icon-position="after"
                        tag="a"
                        href="{{ \App\Filament\Resources\SavedExtractorResource\Pages\CreationWizard::getUrl() }}"
                    >
                        {{ __('Create a new extractor') }}
                    </x-filament::button>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('You can use your previous extractors or use ones you got from the community.') }}
                    </p>
                </div>
            </nav>
        </x-filament::section>
    </div>
</x-filament-panels::page>
