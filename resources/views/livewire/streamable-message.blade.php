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
>
    <div x-show="messages?.length === 0" x-cloak>
        <div class="flex items-center justify-center w-full gap-2 pt-5 pb-10 text-sm">
            <x-filament::loading-indicator class="w-10 h-10" />
            <span>Thinking...</span>
        </div>
    </div>

    <template x-for="(message, index) in messages?.filter(message => message !== null) ?? []" :key="index">
        <div x-html="message" x-bind:wire:key="'message-' + index"></div>
    </template>
</div>
