@push('components')
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>
    <script>
        function Importer() {
            const emptyTokenStats = { tokens: '–', inputTokens: '–', outputTokens: '–', cost: '–' };

            return {
                anthropicApiKey: 'api-key',

                dryRun: true,
                logMessageProgress: false,
                dataset: 'dock100',

                // data: {},
                "data": {!! file_get_contents(base_path('fixtures/dock100/expose.json')) !!} ,

                eventSource: null,
                streamingMessage: null,
                messages: [],

                tokenStats: { ...emptyTokenStats },

                init() {
                    console.log('init');
                    const savedDataset = window.localStorage.getItem('dataset');

                    if (savedDataset) {
                        this.dataset = savedDataset;
                    }

                    this.$watch('dataset', () => window.localStorage.setItem('dataset', this.dataset));

                    this.$watch('dryRun', () => this.preloadDataset());
                    this.$watch('dataset', () => this.preloadDataset());
                },

                preloadDataset() {
                    fetch(`/api/dataset/${this.dataset}`)
                        .then(response => response.json())
                        .then(data => this.data = data)
                        .catch(error => console.error('Could not load dataset', error));
                },

                stream() {
                    this.messages = [];
                    this.streamingMessage = null;
                    this.tokenStats = { ...emptyTokenStats };

                    // JSON event stream at /api/json
                    this.eventSource = new EventSource(`/api/dataset/${this.dataset}/generate?model=${this.dryRun ? 'fake' : 'claude'}`);

                    this.eventSource.addEventListener('onDataProgress', (event) => {
                        if (this.logMessageProgress) {
                            console.log('onMessageProgress', event);
                        }

                        this.streamingMessage = JSON.parse(event.data);
                        this.data = this.streamingMessage.data;
                    });

                    this.eventSource.addEventListener('onMessage', (event) => {
                        console.log('onMessage', event);

                        this.messages.push(JSON.parse(event.data));
                        this.streamingMessage = null;
                    });

                    this.eventSource.addEventListener('onErrorMessage', (event) => {
                        console.error('onErrorMessage', event);

                        const data = JSON.parse(event.data);

                        console.error(data);

                        const title = data?.error?.title || 'Error';
                        const message = data?.error?.message || 'An unknown error occurred.';

                        window.alert(`${title}\n\n${message}`);

                        this.messages.push(data);

                        this.eventSource.close();
                        this.eventSource = null;
                    });

                    this.eventSource.addEventListener('onTokenStats', (event) => {
                        console.log('onTokenStats', event);

                        const stats = JSON.parse(event.data);

                        if (stats.tokens) {
                            this.tokenStats.tokens = stats.tokens;
                        }

                        if (stats.input_tokens) {
                            this.tokenStats.inputTokens = stats.input_tokens;
                        }

                        if (stats.output_tokens) {
                            this.tokenStats.outputTokens = stats.output_tokens;
                        }

                        if (stats.cost) {
                            const inputCentsPer1k = stats.cost.input_cents_per_1k ?? 0;
                            const outputCentsPer1k = stats.cost.output_cents_per_1k ?? 0;

                            const inputCostCents = (stats.input_tokens ?? this.tokenStats.inputTokens ?? 0) / 1000 * inputCentsPer1k;
                            const outputCostCents = (stats.output_tokens ?? this.tokenStats.outputTokens ?? 0) / 1000 * outputCentsPer1k;

                            const totalCostCents = inputCostCents + outputCostCents;
                            const totalCostsEuro = totalCostCents / 100;

                            console.log('inputCentsPer1k', inputCentsPer1k);
                            console.log('outputCentsPer1k', outputCentsPer1k);
                            console.log('inputCostCents', inputCostCents);
                            console.log('outputCostCents', outputCostCents);
                            console.log('totalCostsEuro', totalCostsEuro);

                            this.tokenStats.cost = `${totalCostsEuro.toFixed(3)} €`;
                        }
                    });

                    this.eventSource.addEventListener('onEnd', (event) => {
                        console.log('onEnd', event);
                        this.streamingMessage = null;

                        this.eventSource.close();
                        this.eventSource = null;
                    });

                    this.eventSource.onerror = (event) => {
                        console.error(event);

                        this.eventSource.close();
                        this.eventSource = null;
                    };

                    this.eventSource.onopen = (event) => {
                        console.log(event, event.data, event.details);

                        // Get all events from the stream
                    };
                }
            }
        }
    </script>
@endpush


<main {{ $attributes->class('flex-1 h-screen overflow-hidden flex flex-col') }} x-data="Importer">
    <header class="bg-sky-900 h-16 px-5 flex justify-between items-center shadow border-b border-sky-950">
        <div>
            <h1 class="text-2xl font-semibold" style="font-family: 'Averia Serif Libre', serif;">Data Wizard 🪄✨🧙🏼</h1>
            <link rel="preconnect" href="https://fonts.bunny.net">
            <link href="https://fonts.bunny.net/css?family=averia-serif-libre:300,300i,400,400i,700,700i|abel:200,400,600,700" rel="stylesheet" />
        </div>

        <div class="flex items-start gap-5">
            <label class="text-xs flex flex-col items-start gap-1 text-sky-400">
                Dry Run
                <input type="checkbox" x-model="dryRun" class="mr-2 w-5 h-5 rounded" />
            </label>
            <label class="text-xs flex flex-col gap-1 text-sky-400">
                Dataset
                <select class="text-sky-100 bg-sky-800 px-1 py-1 rounded" x-model="dataset">
                    <option value="elsenstrasse">Elsenstraße</option>
                    <option value="dock100">Dock 100</option>
                    <option value="maxdohrn">Max Dohrn Labs</option>
                </select>
            </label>
            <button
                type="button"
                @click="stream"
                class="ml-4 bg-sky-800 text-sky-100 px-4 py-2 rounded"
            >
                Stream
            </button>
        </div>

        <div>
            <a>Paper</a>
            <p class="group text-sky-200 font-thin">Von <a
                    class="transition-colors group-hover:text-sky-50 text-sky-100 font-semibold"
                    href="https://mateffy.me">Lukas Mateffy</a></p>
        </div>
    </header>
    <div
        class="grid grid-cols-3 overflow-y-hidden grid-rows-1 h-[calc(100vh-4rem)]"
    >
        <div class="col-span-2 px-5 py-5 grid grid-cols-1 items-start overflow-y-auto">
            <template x-for="(estate, estateIndex) in data.estates">
                <x-previews.estate/>
            </template>

            {{--                <x-resource heading="Resource: Objekt">--}}
            {{--                    <p>Hallo Harro</p>--}}

            {{--                    <x-slot:resources>--}}
            {{--                        <x-resource heading="Resource: Gebäude">--}}
            {{--                            <x-resource.fields :data="collect(range(0, 20))->mapWithKeys(fn () => [fake()->word() => fake()->words(random_int(2, 10), true)])" />--}}

            {{--                            <x-slot:resources>--}}
            {{--                                <x-resource heading="Resource: Mietfläche">--}}
            {{--                                    <p>Hallo Harro</p>--}}
            {{--                                </x-resource>--}}
            {{--                            </x-slot:resources>--}}
            {{--                        </x-resource>--}}
            {{--                    </x-slot:resources>--}}
            {{--                </x-resource>--}}
        </div>
        <aside class="bg-sky-900/50 grid grid-rows-5 border-l border-sky-900 shadow-inner">
            <div class="row-span-1 px-5 py-4 ">
                <h2 class="text-xl">Dateien</h2>

                <h3>Inhaltliche Quellen</h3>

                <h3>Referenzierbare Bilder</h3>
            </div>

            <div class="row-span-4 bg-sky-950 px-5 py-4 border-t border-sky-900 overflow-auto">
                <h2 class="text-xl flex items-start justify-between gap-2">
                    <div class="flex flex-col">
                        <span>LLM-Konsole</span>
                        <span class="text-sm opacity-60">Claude3Haiku</span>
                    </div>
                    <table class="text-xs opacity-60 flex flex-col">
                        <tr>
                            <td class="pr-3">Tokens</td>
                            <td x-text="tokenStats.tokens"></td>
                        </tr>
                        <tr>
                            <td>Input</td>
                            <td x-text="tokenStats.inputTokens"></td>
                        </tr>
                        <tr>
                            <td>Output</td>
                            <td x-text="tokenStats.outputTokens"></td>
                        </tr>
                        <tr>
                            <td>Cost</td>
                            <td x-text="tokenStats.cost"></td>
                        </tr>
                    </table>
                </h2>

                <div class="text-green-300 text-xs font-mono">
                    <template x-for="(message, index) in messages.slice().reverse()">
                        <pre :key=index" x-text="JSON.stringify(message, null, 2)"></pre>
                    </template>
                </div>

                <div class="text-orange-300 text-xs font-mono">
                    <template x-if="streamingMessage">
                        <pre x-text="JSON.stringify(streamingMessage, null, 2)"></pre>
                    </template>
                </div>
            </div>
        </aside>
    </div>

    <aside
        x-show="eventSource !== null && streamingMessage === null && messages.length === 0"
        x-cloak
        class="fixed inset-0 backdrop-blur bg-sky-900/50 flex flex-col items-center justify-center"

        x-data="{
            dots: '.',
            progress: 0.0,
            interval: null,
            init() {
                this.interval = setInterval(() => {
                    this.dots = this.dots.length < 3 ? this.dots + '.' : '';
                }, 700);

                this.animate();
            },

            animate() {
                this.progress = Math.min(1.0, this.progress < 1.0 ? this.progress + 0.01 : 0.0);

                requestAnimationFrame(this.animate.bind(this));
            }
        }"
    >
        <dotlottie-player
            src="https://lottie.host/2644eb1f-9b71-40c7-baf4-5d988f3abbe4/KAr8Vf3n0E.lottie"
            background="transparent"
            speed="0.5"
            style=""
            loop
            autoplay
            class="-mt-32 w-64 h-64"
            :style="`transform: scale(${Math.abs(-0.5 + progress) * 0.1 + 0.9}); opacity: ${Math.abs(-0.5 + progress) * 0.5 + 0.5};`"
        ></dotlottie-player>
        <p
            class="text-5xl bg-gradient-to-r from-teal-300 via-teal-600 to-teal-300 bg-clip-text text-transparent drop-shadow-lg bg-[length:200%_200%] transition-asll"
            :style="'background-position: ' + (progress * -200) + '% ' + (progress * -200) + '%;'"
        >
            Simsalabim<span x-text="dots" class="inline-block w-10"></span>
        </p>
    </aside>
</main>
