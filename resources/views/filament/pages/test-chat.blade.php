<x-filament::page
    class="overflow-hidden flex flex-col [&>section]:flex-1 [&>section]:flex [&>section]:flex-col [&>section]:p-0 bg-white dark:bg-gray-950"
    :fullHeight="true"
>
    <style>
        .fi-main {
            max-width: none;
            padding: 0;
        }
    </style>
    <div class="h-[calc(100vh-4rem)] w-full flex flex-col flex-1 overflow-hidden">
        <div
            class="overflow-y-auto w-full flex-col flex flex-1"
            style="scrollbar-gutter: stable; scrollbar-width: thin;"
            x-data="{
                scrolled: false,

                init() {
                    this.$nextTick(() => {
                        $el.scrollTo(0, $el.scrollHeight);
                    });
                },
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
            <?php
                $messages = $this->getChatMessages();
            ?>

            @if ($messages->isEmpty())
                <div class="w-full py-10 px-4 sm:px-6 lg:px-8 flex-1 max-w-2xl mx-auto">
                    <x-filament-tables::empty-state
                        class="[&_.fi-ta-empty-state-icon]:text-primary-600"
                        icon="heroicon-o-chat-bubble-left-right"
                        heading="No messages yet"
                        description="Start by typing your message in the box below."
                    />
                </div>
            @endif

            <x-chat.messages
                class="w-full py-10 px-4 sm:px-6 lg:px-8 flex-1 max-w-2xl mx-auto"
                :$messages
                wire:key="messages"
            />

            <div wire:loading wire:target="start" class="w-full h-10"></div>
        </div>

        <div class="shadow-inner bg-gray-100 dark:bg-gray-900 border-t border-gray-300 dark:border-gray-700 pt-5 pb-10 ">
            <form wire:submit.prevent="send($wire.text)" class="flex gap-5 items-start w-full max-w-2xl mx-auto pr-2" @submit="$wire.text = '';">
                <div class="flex-1">
                    {{ $this->form }}
                </div>

                <div class="flex flex-col gap-5">
                    <x-filament::button
                        type="submit"
                        color="primary"
                        icon="heroicon-o-paper-airplane"
                        icon-position="after"
                        class="w-full"
                    >
                        Send
                    </x-filament::button>
                    <x-filament::link
                        color="gray"
                        icon="heroicon-o-trash"
                        icon-position="after"
                        class="w-full"
                        tag="button"
                        wire:click="resetChat"
                    >
                        Reset
                    </x-filament::link>
                </div>
            </form>
        </div>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"/>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <link href="https://api.mapbox.com/mapbox-gl-js/v3.7.0/mapbox-gl.css" rel="stylesheet">
        <script src="https://api.mapbox.com/mapbox-gl-js/v3.7.0/mapbox-gl.js"></script>
        <script type="text/javascript">
            function DynamicMap({ center, markers, zoom, js }) {
                return {
                    map: null,
                    center,
                    markers,
                    js,
                    init() {
                        this.setupLeaflet();
                        // this.setupMapbox();
                    },

                    setupLeaflet() {
                        this.map = L.map(this.$refs.map).setView(center, zoom);

                        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(this.map);

                        for (const marker of this.markers) {
                            L.marker(marker.coordinates)
                                .addTo(this.map)
                                .bindPopup(marker.label)
                                .openPopup();
                        }

                        this.eval(this.map);
                    },

                    setupMapbox() {
                        const toMapboxCoords = ([lat, lng]) => [lng, lat];

                        console.warn('Setting up Mapbox', toMapboxCoords(Object.values(this.center)));

                        mapboxgl.accessToken = '{{ config('services.mapbox.access_token') }}';
                        this.map = new mapboxgl.Map({
                            container: this.$refs.map,
                            style: 'mapbox://styles/mapbox/streets-v9',
                            projection: 'globe', // Display the map as a globe, since satellite-v9 defaults to Mercator
                            center: toMapboxCoords(Object.values(this.center)),
                            // zoom: this.zoom,
                        });

                        this.map.addControl(new mapboxgl.NavigationControl());

                        // Add markers
                        for (const marker of this.markers) {
                            const coordinates = toMapboxCoords(Object.values(marker.coordinates));

                            console.warn('Adding marker', coordinates);

                            new mapboxgl.Marker(coordinates)
                                .setPopup(new mapboxgl.Popup({ offset: 25 }) // Adds a little offset
                                    .setHTML(marker.label))
                                .addTo(this.map);
                        }

                        // this.eval(this.map);
                    },

                    // Run the JS code in `js` and inject L and map into the global scope
                    eval(map) {
                        if (this.js) {
                            const L = window.L;
                            const mapboxgl = window.mapboxgl;

                            console.warn('Running', this.js, {L, mapboxgl, map});

                            eval(this.js);
                        }
                    },

                    destroy() {
                        this.map.remove();
                    }
                };
            }
        </script>
    </div>

</x-filament::page>
