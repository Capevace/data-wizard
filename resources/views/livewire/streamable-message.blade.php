<div
    @class(['hidden' => !$poll])
    x-data="{
        interval: null,
        messages: null,
        pollUrl: null,

        init() {
            this.$wire.$watch('poll', (poll) => {
                if (poll && this.interval === null) {
                    this.pollUrl = poll;

                    this.interval = setInterval(() => {
                        this.poll();
                    }, 200);
                } else if (!poll && this.interval !== null) {
                    clearInterval(this.interval);
                    this.interval = null;
                    this.messages = null;
                    this.pollUrl = null;
                }
            });
        },

        async poll() {
            if (!this.pollUrl) {
                console.error('No poll URL');
                return;
            }

            fetch(this.pollUrl)
                .then(response => response.json())
                .then(data => {
                    this.messages = data.messages;

                    this.$dispatch('polled', { messages: data.messages });
                });
        },

    }"

    class="flex flex-col gap-5"
>
    <div
        class="flex items-center justify-start w-full gap-5 py-2 mb-5"
        x-show="messages?.length === 0"
        x-cloak
        x-transition:enter="animate-fade-down animate-alternate animate-duration-300 duration-300 "
        x-transition:leave="animate-fade-down animate-alternate-reverse animate-duration-200 duration-200 "
    >
        <x-filament::loading-indicator @class(['w-8 h-8 text-gray-600'])/>

        <div class="font-semibold">{{ __('Thinking...') }}</div>
    </div>

    <template x-for="(message, index) in messages?.filter(message => message !== null) ?? []" :key="index">
        <div
            x-transition:enter="animate-fade-down animate-alternate animate-duration-300 duration-300 "
            x-transition:leave="animate-fade-down animate-alternate-reverse animate-duration-200 duration-200 "
            x-html="message" x-bind:wire:key="'message-' + index"
        ></div>
    </template>
</div>
