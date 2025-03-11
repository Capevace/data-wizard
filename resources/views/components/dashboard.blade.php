<x-filament-panels::page class="fi-dashboard-page">
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <div class="flex justify-around items-start relative">
        <section class="flex-1">
            <h1 class="font-semibold mb-2 ">{{ config('app.name') }}</h1>
            <div class="prose dark:prose-invert">
                <h2 class="text-primary-600 dark:text-primary-400">Journey into the unknown</h2>
                <p>
                    Well, not really. You're here to extract structured data from unstructured sources. The Wizard is here to help you do that.
                </p>
                <h4 class="mb-4 text-primary-600 dark:text-primary-400">Here's how you can extract some data:</h4>
                <ol class="text-sm mb-8 text-gray-600 dark:text-gray-300">
                    <li>Define your JSON datamodel using an extractor</li>
                    <li>Upload your dataset</li>
                    <li>Run an extraction</li>
                </ol>

                <h4 class="mb-4 text font-semibold text-primary-600 dark:text-primary-400">You can integrate extraction in your app:</h4>
                <ol class="text-sm text-gray-600 dark:text-gray-300">
                    <li>Embed your extractor using the iFrame SDK or redirect to a presigned URL</li>
                    <li>Let your users run the embedded extraction flow or use the API</li>
                    <li>Use the extracted data in your code</li>
                </ol>
            </div>
        </section>
        <img
            src="{{ url('images/wizard1.png') }}"
            alt="Dwarf"
            class="z-0 absolute h-[120%] md:h-auto md:relative right-0 opacity-20 dark:opacity-10 md:opacity-100 md:block w-[30rem] md:w-[20rem] lg:w-[26rem] drop-shadow-lg object-right object-contain flex-shrink animate-pulse md:dark:opacity-80"
            style="animation-duration: 30s;"
        />
    </div>

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
        <x-filament::section class="[&_.fi-section-content-ctn]:h-full [&_.fi-section-content]:h-full">
            <div class="flex flex-col h-full">
                <div class="flex items-start gap-3 md:gap-10 mb-10 flex-1">
                    <div class="flex-1">
                        <h3 class="md:text-lg font-medium mb-2">{{ __('Define your datamodel using an extractor') }}</h3>
                        <p class="text-sm md:text-base text-gray-500 dark:text-gray-400">{{ __('To extract structured data from your source material, define a JSON schema that describes the shape of the data you want to receive.') }}</p>
                    </div>
                    <div>
                        <x-icon name="bi-braces" class="w-8 h-8 sm:w-12 sm:h-12 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
                <nav class="grid sm:grid-cols-2 items-start w-full gap-8">
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
            </div>
        </x-filament::section>

        <x-filament::section class="[&_.fi-section-content-ctn]:h-full [&_.fi-section-content]:h-full">
            <div class="flex flex-col h-full">
                <div class="flex items-start gap-3 md:gap-10 mb-10 flex-1">
                    <div class="flex-1">
                        <h3 class="md:text-lg font-medium mb-2">{{ __('Upload a dataset') }}</h3>
                        <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">{{ __('If you have some files you want to keep running extractions on, upload them into a reusable bucket.') }}</p>
                    </div>
                    <div>
                        <x-icon name="bi-database-add" class="w-8 h-8 sm:w-12 sm:h-12 text-primary-600 dark:text-primary-400" />
                    </div>
                </div>
                <nav class="grid sm:grid-cols-1 items-start w-full gap-8">
                    <div class="flex flex-col gap-2">
                        <x-filament::button
                            icon="bi-upload"
                            icon-position="after"
                            color="gray"
                            tag="a"
                            href="{{ \App\Filament\Resources\ExtractionBucketResource\Pages\CreateExtractionBucket::getUrl() }}"
                        >
                            {{ __('Upload some files') }}
                        </x-filament::button>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ __('To start extracting some data, you\'ll need to create a new bucket and upload some files to it. Buckets can be reused across multiple extractors and runs.') }}
                        </p>
                    </div>
                </nav>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
