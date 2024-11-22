<div
    @class(['hidden' => !$poll])
    x-data="{
        interval: null,
        messages: [],
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
                    this.messages = [];
                    this.pollUrl = null;
                }
            });
        },

        async fetchScripts() {
            if (window.STREAMABLE_MESSAGE_PURIFY && window.STREAMABLE_MESSAGE_MARKDOWN) {
                return [window.STREAMABLE_MESSAGE_PURIFY, window.STREAMABLE_MESSAGE_MARKDOWN];
            }

            const [purify, markdown] = await Promise.all([
                import('https://esm.sh/dompurify'),
                import('https://esm.sh/marked'),
            ]);

            console.log(purify, markdown);
            window.STREAMABLE_MESSAGE_PURIFY = purify.default;
            window.STREAMABLE_MESSAGE_MARKDOWN = markdown.marked;

            return [window.STREAMABLE_MESSAGE_PURIFY, window.STREAMABLE_MESSAGE_MARKDOWN];
        },

        markdown(message) {
            this.fetchScripts();
            if (window.STREAMABLE_MESSAGE_MARKDOWN && window.STREAMABLE_MESSAGE_PURIFY) {
                const sanitized = window.STREAMABLE_MESSAGE_PURIFY.sanitize(message);
                return window.STREAMABLE_MESSAGE_MARKDOWN(message);
            }

            return message;
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
                    console.log(data.messages);

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
        x-transition:leave="animate-fade-down animate-alternate-reverse animate-duration-200 duration-200 absolute"
    >
        <x-filament::loading-indicator @class(['w-8 h-8 text-gray-600'])/>

        <div class="font-semibold">{{ __('Thinking...') }}</div>
    </div>

    <template x-for="(message, index) in messages?.filter(message => message !== null) ?? []" :key="index">
        <div
            x-transition:enter="animate-fade-down animate-alternate animate-duration-300 duration-300 "
            x-transition:leave="animate-fade-down animate-alternate-reverse animate-duration-200 duration-200 "
            x-html="message"
        ></div>
    </template>
</div>
