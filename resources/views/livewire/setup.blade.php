<x-filament-panels::page.simple>
    <style>
        .fi-simple-main {
            max-width: 41rem;
        }
    </style>

    @if ($step === 0)
        <div class="w-full mx-auto prose dark:prose-invert">
            <h3>Welcome to Data Wizard! 👋</h3>
            <p>It's great to have you here! To get started, we need to create your superadmin account. This account will have full access to manage your {{ config('app.name') }} instance.  Just fill in the details below and you'll be up and running in no time! ✨</p>
            @if ($this->show_environment)
            <p>In order for everything to work correctly, please make sure that the `APP_KEY` environment variable is set, available to the application and kept secure. <a href="https://laravel.com/docs/11.x/encryption#:~:text=Before%20using%20Laravel%27s,Laravel%27s%20installation.">Read more in the documentation.</a></p>
            @endif
        </div>

        @if ($this->show_environment)
            <div class="border dark:border-gray-700 rounded-md overflow-hidden dark:text-gray-100">
                <div class="px-2 py-1 bg-gray-100 dark:bg-gray-800 dark:text-gray-100 font-mono border-b dark:border-gray-700" style="font-size: 0.5rem;">.env</div>
                <pre class="!font-mono shadow-inner !text-xs px-2 py-2 dark:bg-gray-950 dark:text-gray-300">{{ $this->generated_environment }}</pre>
            </div>
        @endif
    @endif

    @if ($step === 1)
        <div class="w-full mx-auto prose dark:prose-invert">
            <h3>Superadmin Account</h3>
            <p>Next, we need to create your superadmin account. This account will have full access to manage your {{ config('app.name') }} instance.</p>
        </div>
        <form class="mb-5" wire:submit.prevent="finish">
            {{ $this->form }}

            <button type="submit" class="hidden"></button>
        </form>
    @endif

    <nav class="flex justify-between">
        @if ($this->step > 0)
            <x-filament::button
                wire:click="previous"
                color="gray"
                icon="heroicon-o-arrow-left"
            >
                Previous
            </x-filament::button>
        @else
            <div></div>
        @endif


        @if ($this->step < self::MAX_STEPS && $this->step !== 1)
            <x-filament::button
                wire:click="next"
                color="primary"
                icon="heroicon-o-arrow-right"
                icon-position="after"
            >
                Continue
            </x-filament::button>
        @elseif ($this->step === 1)
            <x-filament::button
                wire:click="finish"
                color="primary"
                icon="heroicon-o-check"
                icon-position="after"
            >
                Create and get started
            </x-filament::button>
        @else
            <div></div>
        @endif
    </nav>
</x-filament-panels::page.simple>
