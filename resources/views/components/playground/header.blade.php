<div
    @class([
        'relative -mx-5 -mt-3 px-5 pb-4 pt-4 overflow-hidden',
        'bg-gradient-to-r from-fuchsia-500/20 to-cyan-500/20' => true
    ])
>
    <x-playground.animated-background :status="$this->run?->status" />

    <nav class="flex items-center justify-between gap-4 border-b border-gray-200 dark:border-gray-700">
        <x-filament::button
            size="xs"
            color="gray"
            icon="heroicon-o-x-mark"
            icon-position="after"
            wire:click="resetPlayground"
        >
            Reset
        </x-filament::button>

        @if ($this->run?->status->shouldPoll())
            <div
                class="text-sm flex items-center gap-1  opacity-50"
                wire:key="extracting-indicator"
            >
                <p>Extracting</p>
                <x-filament::loading-indicator class="w-4 h-4" />
            </div>
        @else
            <div
                class="text-sm flex items-center gap-1  opacity-50"
                wire:loading.flex
                wire:key="saving-indicator"
            >
                <p>Saving</p>
                <x-filament::loading-indicator class="w-4 h-4" />
            </div>
            <div
                class="text-sm flex items-center gap-1 text-success-400 font-medium"
                wire:loading.flex.remove
                wire:key="saved-indicator"
                x-data="{
                    show: false,
                    timeout: null,

                    init() {
                        this.$wire.watch('show_saved_indicator', (show_saved_indicator) => {
                            this.show = show_saved_indicator;

                            if (this.show) {
                                this.showSavedIndicator();
                            } else {
                                clearTimeout(this.timeout);
                            }
                        });
                    },

                    showSavedIndicator() {
                        this.show = true;

                        clearTimeout(this.timeout);

                        this.timeout = setTimeout(() => {
                            this.show = false;
                            this.$wire.show_saved_indicator = false;
                        }, 1000);
                    }
                }"
            >
                <p x-show="show">Saved</p>
                <x-icon x-show="show" name="bi-check" class="w-5 h-5" />
            </div>
        @endif

        <x-filament::button
            size="xs"
            icon="heroicon-s-play"
            icon-position="after"
            wire:click="startRun"
        >
            Start
        </x-filament::button>
    </nav>

    <div class="mt-4">
        <div class="flex items-center gap-5">
            @if ($this->run?->status->shouldPoll())
                <x-filament::loading-indicator class="w-12 h-12" />
            @elseif ($icon = $this->run?->status->getIcon())
                <x-icon :name="$icon" class="w-10 h-10" />
            @endif

            <p class="text-2xl">{{ $this->run?->status->getLabel() ?? 'Ready to start' }}</p>
        </div>

        @if ($this->run?->error)
            <div class="mt-3">
                <x-playground.error :error="$this->run->error" />
            </div>
        @endif
    </div>

    <x-filament::tabs class="w-min !p-1 m-1 absolute right-0 bottom-0">
        <x-filament::tabs.item
            icon="bi-image"
            label="Preview"
            :active="$this->tab === 'gui'"
            alpine-active="$wire.tab === 'gui'"
            wire:click="$set('tab', 'gui')"
        >
            GUI
        </x-filament::tabs.item>
        <x-filament::tabs.item
            icon="bi-code-slash"
            label="JSON"
            :active="$this->tab === 'json'"
            alpine-active="$wire.tab === 'json'"
            wire:click="$set('tab', 'json')"
        >
            JSON
        </x-filament::tabs.item>
    </x-filament::tabs>
</div>
