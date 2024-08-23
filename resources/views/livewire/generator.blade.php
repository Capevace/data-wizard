<div
    x-ignore
	ax-load
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('GeneratorComponent') }}"
    x-data="GeneratorComponent"
    class="grid grid-cols-1 gap-6 md:grid-cols-3"
    @start-streaming.window="startStreaming($event.detail.sourceUrl)"
    @if (in_array($this->run?->status->value, ['running', 'pending']))
        wire:poll.300ms="refreshJson"
    @endif
>
    <div class="col-span-1 md:col-span-2">
        <x-filament::tabs class="mb-5">
            <x-filament::tabs.item
                alpine-active="tab === 'gui'"
                @click="tab = 'gui'"
                icon="heroicon-o-newspaper"
            >
                GUI
            </x-filament::tabs.item>
            <x-filament::tabs.item
                alpine-active="tab === 'json'"
                @click="tab = 'json'"
                icon="heroicon-o-code-bracket"
            >
                JSON
            </x-filament::tabs.item>
        </x-filament::tabs>

        <template x-if="tab === 'gui'">
            <div class="flex flex-col gap-4">
                <template x-for="(estate, estateIndex) in $wire.partialResultJson?.data?.estates" :key="estateIndex">
                    <x-previews.estate/>
                </template>
            </div>
        </template>

        <template x-if="tab === 'json'">
            <pre
                x-data="{
                    get json() {
                        return window.prettyPrint(this.$wire.partialResultJson);
                    }
                }"
                class="text-xs prettyjson"
                x-html="json"
            ></pre>
        </template>
    </div>

    <div class="grid grid-cols-1 gap-6 mb-6 col-span-1 md:col-span-1">
        @if ($this->run->status === \App\Models\ExtractionRun\RunStatus::Failed)
            <x-filament::section
                compact
                :heading="__('Error')"
                icon="heroicon-o-exclamation-triangle"
                icon-color="danger"
                class="col-span-1"
            >
                @if ($message = $this->run->error['message'] ?? null)
                    <p class="text-sm text-red-500 break-all">{{ $message }}</p>
                @else
                    <pre class="text-xs text-red-500">{{ json_encode($this->run->error, JSON_PRETTY_PRINT) }}</pre>
                @endif
            </x-filament::section>
        @endif

        <x-filament::section
            :heading="$this->run?->status->getLabel()"
            :icon="$this->run?->status->getIcon()"
            :icon-color="$this->run?->status->getColor()"
            compact
            class="col-span-1"
        >
            <div class="grid grid-cols-1 gap-1 gap-y-4">
                <x-generator.key-value icon="bi-robot" label="Model">
                    <x-filament::badge size="lg" class="w-fit">
                        anthropic/claude-sonnet-3.5
                    </x-filament::badge>
                </x-generator.key-value>

                <x-generator.key-value icon="heroicon-o-clock" label="Duration">
                    <span class="text-lg">{{ $this->run?->status->formatDate($this->run?->created_at) }}</span>
                </x-generator.key-value>
            </div>
        </x-filament::section>

        <x-filament::section
            heading="Ausführung"
            compact
            class="col-span-1"
        >
            <p>{{ $this->bucket->description }}</p>
        </x-filament::section>

        <x-filament::section
            heading="LLM Costs"
            icon="heroicon-o-currency-euro"
            compact
            class="col-span-1"
        >
            @if ($stats = $this->run?->token_stats)
                <x-generator.cost :stats="$stats" class="col-span-1" />
            @endif
        </x-filament::section>
    </div>
</div>
