<div
    {{--    x-ignore--}}
    {{--	ax-load--}}
    {{--    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('GeneratorComponent') }}"--}}
    x-data="GeneratorComponent({ debugModeEnabled: @entangle('debugModeEnabled') })"
    class="grid grid-cols-1 gap-6 md:grid-cols-3 items-start"
    @start-streaming.window="startStreaming($event.detail.sourceUrl)"
    @if (in_array($this->run?->status->value, ['running', 'pending']))
        wire:poll.2s="refreshJson"
    @endif
>
    <div class="col-span-1 md:col-span-2">
        <x-filament::tabs class="mb-5">
            <x-filament::tabs.item
                alpine-active="tab === 'gui'"
                @click="
                    tab = 'gui';
                    $wire.set('actorTab', null);
                "
                icon="heroicon-o-newspaper"
            >
                GUI
            </x-filament::tabs.item>
            <x-filament::tabs.item
                alpine-active="tab === 'json'"
                @click="
                    tab = 'json';
                    $wire.set('actorTab', null);
                "
                icon="heroicon-o-code-bracket"
            >
                JSON
            </x-filament::tabs.item>

            @foreach ($this->run?->actors ?? [] as $actor)
                <x-filament::tabs.item
                    alpine-active="$wire.actorTab === '{{ $actor->id }}'"
                    @click="
                        $wire.set('actorTab', '{{ $actor->id }}');
                    "
                    icon="heroicon-o-user-circle"
                >
                    {{ 'Chat ' . ($loop->index + 1) }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>

        <template x-if="tab === 'gui'">
            <div class="flex flex-col gap-4">
                <x-llm-magic::json-schema
                    :schema="$this->schema"
                    :disabled="!$this->run?->status->isDataEditable()"
                />
            </div>
        </template>

        <template x-if="tab === 'json'">
            <pre
                x-data="{
                    get json() {
                        return window.prettyPrint(this.$wire.resultData);
                    }
                }"
                class="text-xs prettyjson"
                x-html="json"
            ></pre>
        </template>

        <template x-if="tab === 'chat'">
            <div class="flex flex-col gap-4">
                @foreach ($this->messages ?? [] as $message)
                    @php /** @var \App\Models\ActorMessage $message */ @endphp

                    <div
                        @class([
                            'flex items-center',
                            'justify-start' => $message->role === \Mateffy\Magic\Chat\Prompt\Role::Assistant,
                            'justify-end' => $message->role !== \Mateffy\Magic\Chat\Prompt\Role::Assistant,
                        ])
                    >
                        <div class="max-w-xl text-xs bg-white dark:bg-gray-800 dark:text-gray-200 border dark:border-gray-700 rounded-lg p-4 shadow-md">
                            @if ($message->type === \App\Models\Actor\ActorMessageType::Text)
                                <pre class="whitespace-pre-wrap w-full bg-transparent text-inherit">{{ $message->text ?? json_encode($message->json) }}</pre>
                            @elseif ($message->type === \App\Models\Actor\ActorMessageType::Base64Image)
                                <img
                                    src="data:image/png;base64,{{ $message->base64Image()?->imageBase64 ?? '' }}"
                                    class="w-full aspect-video object-center object-cover"
                                />
                            @elseif ($message->type === \App\Models\Actor\ActorMessageType::FunctionInvocation)
                                <div
                                    x-data="{
                                    json: @js($message->data('call.arguments')),
                                    get prettyJson() {
                                        return window.prettyPrint(this.json);
                                    }
                                }"
                                    class="min-w-0 flex-1"
                                >
                                    <pre
                                        class="prettyjson"
                                        x-html="prettyJson"
                                    ></pre>
                                </div>
                            @elseif ($message->type === \App\Models\Actor\ActorMessageType::FunctionOutput)
                                <div
                                    x-data="{
                                    json: @js($message->data('output')),
                                    get prettyJson() {
                                        return window.prettyPrint(this.json);
                                    }
                                }"
                                    class="min-w-0 flex-1"
                                >
                                    <pre
                                        class="prettyjson"
                                        x-html="prettyJson"
                                    ></pre>
                                </div>
                            @else
                                <div
                                    x-data="{
                                    json: @js($message->json),
                                    get prettyJson() {
                                        return window.prettyPrint(JSON.parse(this.json));
                                    }
                                }"
                                    class="min-w-0 flex-1"
                                >
                                    <pre
                                        class="prettyjson"
                                        x-html="prettyJson"
                                    ></pre>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </template>
    </div>

    <div class="grid grid-cols-1 gap-6 mb-6 col-span-1 md:col-span-1">
        @if ($this->run->status === \App\Models\ExtractionRun\RunStatus::Failed)
            <x-filament::section
                compact
                :heading="__('Error')"
                icon="heroicon-o-exclamation-triangle"
                icon-color="danger"
                class="col-span-1"
            >
                @if ($title = $this->run->error['title'] ?? null)
                    <p class="text-sm font-semibold text-danger-500 dark:text-danger-400">{{ $title }}</p>
                @endif
                @if ($errorMessage = $this->run->error['message'] ?? null)
                    <p class="text-sm text-danger-500 dark:text-danger-400 mb-2">{{ $errorMessage }}</p>
                @endif
                @if ($trace = $this->run->error['trace'] ?? null)
                    <pre class="text-xs text-gray-500 dark:text-gray-400 overflow-x-auto bg-transparent">{{ $trace }}</pre>
                @endif
            </x-filament::section>
        @endif

        <x-filament::section
            :heading="$this->run?->status->getLabel()"
            :icon="$this->run?->status->getIcon()"
            :icon-color="$this->run?->status->getColor()"
            compact
            class="col-span-1"
        >
            <div class="grid grid-cols-1 gap-1 gap-y-4">
                <x-generator.key-value
                    icon="heroicon-o-clock"
                    :label="__('Duration')"
                >
                    <span class="text-lg">{{ $this->run?->formatted_duration }}</span>
                </x-generator.key-value>

                <x-generator.key-value
                    icon="heroicon-o-list-bullet"
                    :label="__('Steps')"
                >
                    <span class="text-lg">
                        {{ __(':completed / :estimated estimated steps', [
                            'completed' => $this->run?->getCompletedSteps(),
                            'estimated' => $this->run?->getEstimatedSteps(),
                        ]) }}
                    </span>
                </x-generator.key-value>
            </div>
        </x-filament::section>

        <x-filament::section
            icon="bi-robot"
            :description="__('Extractor')"
            :heading="$this->run->saved_extractor?->label ?? __('Extraction')"
            compact
            class="col-span-1"
        >
            <x-slot:header-actions>
                <x-filament::icon-button
                    icon="heroicon-o-pencil-square"
                    color="gray"
                    tag="a"
                    :href="\App\Filament\Resources\SavedExtractorResource::getUrl('edit', ['record' => $this->run->saved_extractor?->id])"
                />
            </x-slot:header-actions>

            <x-generator.key-value
                :icon="\App\Models\ApiKey\ApiKeyProvider::tryFromModelString($this->run?->model ?? $this->saved_extractor->model)?->getIcon() ?? 'bi-robot'"
                label="Model"
                class="mb-5"
            >
                {{ \Mateffy\Magic::models()->get($this->run?->model ?? $this->saved_extractor->model) ?? $this->run?->model ?? $this->saved_extractor->model }}
            </x-generator.key-value>

            <x-generator.key-value
                icon="heroicon-o-beaker"
                label="Strategy"
                class="mb-5"
            >
                @if ($class = \Mateffy\Magic::getExtractionStrategies()[$this->run?->strategy ?? ''] ?? null)
                    {{ $class::getLabel() }}
                @else
                    {{ ucfirst($this->run?->strategy) }}
                @endif
            </x-generator.key-value>

            <x-generator.key-value
                icon="heroicon-o-cube-transparent"
                label="Chunk Size"
                class="mb-5"
            >
                {{ $this->run?->chunk_size ?? config('llm-magic.artifacts.default_max_tokens') }} {{ __('tokens') }}
            </x-generator.key-value>

            <x-generator.key-value
                icon="heroicon-o-adjustments-horizontal"
                label="Output Instructions"
            >
                @if ($instructions = $this->run->saved_extractor?->output_instructions)
                    <div class="prose prose-sm dark:prose-invert">
                        <p class="whitespace-pre-wrap">{{ $instructions }}</p>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No custom instructions</p>
                @endif
            </x-generator.key-value>


        </x-filament::section>

        <x-filament::section
            :heading="__('LLM Costs')"
            icon="heroicon-o-currency-euro"
            compact
            class="col-span-1"
        >
            <x-generator.cost
                :stats="$this->run->getEnrichedTokenStats()"
                class="col-span-1"
            />
        </x-filament::section>
    </div>
</div>
