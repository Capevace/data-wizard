<body>
<script src="//unpkg.com/alpinejs"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    window.StreamStore = Alpine.store('stream', {
        packet: null,
        formatted: null,

        parsePacket(packet) {
            const text = atob(packet);
            const json = JSON.parse(text);

            // if (json.obj && json.obj.functionCall && json.obj.functionCall.arguments && json.obj.functionCall.arguments.arguments_raw) {
            //     const partial = window.partialParse(json.obj.functionCall.arguments.arguments_raw);
            //
            //     delete json.obj.functionCall.arguments.arguments_raw;
            //     json.obj.functionCall.arguments = partial;
            // } else {
            //     console.log(json.obj.functionCall.arguments);
            // }

            const formatted = JSON.stringify(json, null, 2);

            this.formatted = formatted;
            this.packet = json;
        }
    });
    console.log('Alpine initialized');

    function partialParse(s) {
        var tail = [];
        var inString = false;
        var stringStart = -1;
        s = s.replace(/\\r\\n/g, '');

        for (var i = 0; i < s.length; i++) {
            if (s[i] === '"' && (i === 0 || s[i - 1] !== '\\\\')) {
                inString = !inString;
                if (inString) {
                    stringStart = i;
                } else {
                    stringStart = -1;
                }
            } else if (!inString && s[i] === '{') {
                tail.push('}');
            } else if (!inString && s[i] === '[') {
                tail.push(']');
            } else if (!inString && s[i] === '}') {
                tail.splice(tail.lastIndexOf('}'), 1);
            } else if (!inString && s[i] === ']') {
                tail.splice(tail.lastIndexOf(']'), 1);
            }
        }

        // Close an unterminated string
        if (inString) {
            s += '"';
        }

        function getNonWhitespaceCharacterOfStringAt(s, i) {
            while (s[i].match(/\\s/) !== null) {
                i--;
            }
            return {
                character: s[i],
                index: i
            };
        }

        var lastCharacter = getNonWhitespaceCharacterOfStringAt(s, s.length - 1);
        if (lastCharacter.character === ',') {
            s = s.slice(0, lastCharacter.index);
        }
        tail = tail.reverse();
        return JSON.parse(s + tail.join(''));
    }

    window.partialParse = partialParse;

    window.packet = null;

    function render() {
        if (window.packet !== null) {
            Alpine.store('stream').parsePacket(window.packet);
            window.packet = null;
        }
    }

    setInterval(render, 50);
</script>

<div id="content">
    <div x-data="{}">
        <template x-if="$store.stream.packet.obj?.data?.estates?.length > 0" x-for="estate in $store.stream.packet.obj.data.estates">
            <div :key="estate.id">
                <div
                    class="bg-gray-100 border p-4"
                >
                    <p>Objekt</p>
                    <p x-text="estate.name"></p>
                    <p>
                        <span x-text="estate.address?.street"></span>&nbsp;<span x-text="estate.address?.number"></span>
                    </p>
                    <p>
                        <span x-text="estate.address?.postal_code"></span>&nbsp;<span x-text="estate.address?.city"></span>
                    </p>
                </div>

                <p>Images:</p>
                <div class="flex">
                    <template
                        x-if="estate.images?.length > 0"
                        x-for="image in estate.images"
                    >
                        <img
                            :key="image ?? 'none'"
                            :src="'http://magic-import.test/images/' + image?.split('#')[1]"
                            class="w-1/5"
                        />
                    </template>
                </div>

                <p>Buildings:</p>
                <template
                    x-if="estate.buildings?.length > 0"
                    x-for="building in estate.buildings"
                >
                    <div
                        :key="building.id"
                        class="bg-gray-100 border p-4"
                    >
                        <p>Gebäude</p>
                        <p x-text="building.name"></p>
                        <p x-text="building.description"></p>

                        <p>Images:</p>
                        <div class="flex">
                            <template
                                x-if="building.images?.length > 0"
                                x-for="image in building.images"
                            >
                                <img
                                    :key="image ?? 'none'"
                                    :src="'http://magic-import.test/images/' + image?.split('#')[1]"
                                    class="w-1/5"
                                />
                            </template>
                        </div>

                        <template
                            x-if="building.spaces?.length > 0"
                            x-for="space in building.spaces"
                        >
                            <div
                                :key="space.id"
                                class="bg-gray-100 border p-4"
                            >
                                <p>Mietfläche</p>
                                <p x-text="space.name"></p>
                                <p x-text="space.description"></p>
                                <p x-text="space.area"></p>

                                <p>Images:</p>
                                <div class="flex">
                                    <template
                                        x-if="space.images?.length > 0"
                                        x-for="image in space.images"
                                    >
                                        <img
                                            :key="image ?? 'none'"
                                            :src="'http://magic-import.test/images/' + image?.split('#')[1]"
                                            class="w-1/5"
                                        />
                                    </template>
                                </div>

                                <p>Floorplans:</p>
                                <div class="flex">
                                    <template
                                        x-if="space.floorplans?.length > 0"
                                        x-for="image in space.floorplans"
                                    >
                                        <img
                                            x-show="image"
                                            :key="image ?? 'none'"
                                            :src="'http://magic-import.test/images/' + image?.split('#')[1]"
                                            class="w-1/5"
                                        />
                                    </template>
                                </div>

                                <ul>
                                    <template x-for="feature in space.features">
                                        <li x-text="feature"></li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </template>

{{--        <template--}}
{{--            x-if="$store.stream.packet.obj.functionCall.arguments.buildings"--}}
{{--            x-for="building in $store.stream.packet.obj.functionCall.arguments.buildings"--}}
{{--        >--}}
{{--            <div--}}
{{--                class="bg-gray-100 border p-4"--}}
{{--                :key="building.id"--}}
{{--            >--}}
{{--                <p>Gebäude</p>--}}
{{--                <p x-text="building.name"></p>--}}
{{--                <p x-text="building.description"></p>--}}

{{--                <template--}}
{{--                    x-for="space in building.spaces ?? []"--}}
{{--                >--}}
{{--                    <div--}}
{{--                        class="bg-gray-100 border p-4"--}}
{{--                        :key="space.id"--}}
{{--                    >--}}
{{--                        <p>Mietfläche</p>--}}
{{--                        <p x-text="space.name"></p>--}}
{{--                        <p x-text="space.description"></p>--}}
{{--                        <p x-text="space.area"></p>--}}

{{--                        <ul>--}}
{{--                            <template x-for="feature in space.features">--}}
{{--                                <li x-text="feature"></li>--}}
{{--                            </template>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </template>--}}
{{--            </div>--}}
{{--        </template>--}}



        <pre x-text="$store.stream.formatted"></pre>
    </div>
</div>
