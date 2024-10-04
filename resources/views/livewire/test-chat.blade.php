<div class="w-full px-4 sm:px-6 lg:px-8 py-10 h-screen flex flex-col gap-5">
    <div
        class="overflow-y-auto w-full flex-1"
        style="scrollbar-gutter: stable; scrollbar-width: thin;"
        x-data="{
            scrolled: false,
        }"
        @scroll="
            if ($el.scrollTop + $el.clientHeight >= $el.scrollHeight - 10) {
                scrolled = false;
            } else {
                scrolled = true;
            }
        "
        {{-- Animate scroll to bottom --}}
        @polled.window="
            if (!scrolled) {
                $el.scrollTo(0, $el.scrollHeight);
            }
        "
    >
        <x-chat.messages class="w-full max-w-2xl mx-auto" />

        <div wire:loading wire:target="start" class="w-full h-10"></div>
    </div>

    <form wire:submit.prevent="send($wire.text)" class="flex gap-5 items-start w-full max-w-2xl mx-auto pr-2" @submit="$wire.text = '';">
        <div class="flex-1">
            {{ $this->form }}
        </div>

        <div class="">
            <x-filament::button
                type="submit"
                color="primary"
                icon="heroicon-o-paper-airplane"
                icon-position="after"
                class="w-full"
            >
                Send
            </x-filament::button>
        </div>
    </form>
</div>
