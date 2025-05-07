<div
    class="relative w-full max-h-[calc(100vh-4rem)] grid grid-cols-3"
    @if ($this->run?->status->shouldPoll())
        wire:poll
    @endif
>
    <aside class="col-span-1 bg-gray-100 dark:bg-gray-800 px-5 py-3 flex flex-col gap-5 h-full overflow-y-hidden">
        <x-playground.header />

        <div class="flex-1 space-y-5 px-5 py-4 -mx-5 overflow-y-auto -mt-5" style="zoom: 0.9">
            <form>
                {{ $this->form }}
            </form>

            <x-playground.codemirror label="JSON Schema" state-path="schema_json" class="w-full" />
            <x-playground.codemirror label="Prompt" state-path="output_instructions" language="markdown" />
        </div>

        <div>
            <livewire:playground.bucket :bucket-id="$this->bucket->id" />
        </div>
    </aside>
    <main class="sticky top-0 col-span-2 bg-gray-50 dark:bg-gray-950 py-5 border-l border-gray-200">

        @if ($this->run?->status === \App\Models\ExtractionRun\RunStatus::Failed)
            <div class="p-5">
                <x-filament::card>
                    <h2 class="text-lg font-semibold">Error</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $this->run?->error['message'] ?? 'No error message' }}
                    </p>
                </x-filament::card>
            </div>
        @endif

        <div class="px-5">
            <template x-if="$wire.tab === 'gui'">
                <div class="flex flex-col gap-4">
                    <x-llm-magic::json-schema
                        :schema="json_decode($this->schema_json, true)"
                        :disabled="!$this->run?->status->isDataEditable()"
                    />
                </div>
            </template>

            <template x-if="$wire.tab === 'json'">
{{--                <pre--}}
{{--                    x-data="{--}}
{{--                        get json() {--}}
{{--                            return window.prettyPrint(@js($this->run?->partial_data));--}}
{{--                        }--}}
{{--                    }"--}}
{{--                    class="text-xs prettyjson"--}}
{{--                    x-html="json"--}}
{{--                ></pre>--}}
                <x-playground.codemirror
                    label="Prompt"
                    state-path="resultData"
                    language="json"
                    disabled
                />
            </template>
        </div>
    </main>
</div>
