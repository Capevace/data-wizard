@props([
    /** @var \Mateffy\Magic\LLM\Message\Message $message */
    'message',

    'streaming' => false,
    'isSecondLast' => false,
    'isCurrent' => false,
])

@switch ($message::class)
    @case(\Mateffy\Magic\LLM\Message\TextMessage::class)
        <x-chat.bubble class="flex-1 mb-5">
            <p class="whitespace-pre-wrap w-full bg-transparent text-inherit font-sans">{{ trim($message->text()) }}</p>
        </x-chat.bubble>
        @break
    @case(\Mateffy\Magic\LLM\Message\FunctionInvocationMessage::class)
        <x-chat.bubble
            @class([
                'flex-1 flex items-center gap-3 font-semibold',
                'rounded-t-lg border-b border-gray-200 dark:border-gray-700' => !$isCurrent,
                'rounded-lg mb-5' => $isCurrent
            ])
        >
            <x-icon name="bi-robot" class="w-5 h-5 text-green-500" />
            <span class="flex-1">{{ str($message->call->name)->snake()->replace('_', ' ')->title() }}</span>

            @if ($streaming && $isCurrent)
                <x-filament::loading-indicator class="w-5 h-5" />
            @endif
        </x-chat.bubble>
        @break
    @case(\Mateffy\Magic\LLM\Message\FunctionOutputMessage::class)
        <x-chat.bubble class="flex-1 mb-5" rounding="rounded-b-lg">
            <pre
                class="w-full bg-transparent text-inherit {{ is_array($message->output) ? '[&>.json-value]:!text-primary-400 [&>.json-string]:!text-primary-400 [&>.json-key]:!text-primary-100 dark:[&>.json-key]:!text-primary-800' : '' }}"
                style="font-size: .55rem"
                x-html="window.prettyPrint({{ is_array($message->output) ? json_encode($message->output, JSON_PRETTY_PRINT) : $message->output }})"
            ></pre>
        </x-chat.bubble>
        @break
    @case(\Mateffy\Magic\LLM\Message\MultimodalMessage::class)
        <x-chat.bubble class="flex-1 mb-5">
            @foreach ($message->content as $content)
                @if ($content instanceof \Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image)
                    <img
                        src="data:image/png;base64,{{ $content->imageBase64 }}"
                        alt="Image"
                        class="w-full aspect-video object-cover"
                    />
                @elseif ($content instanceof \Mateffy\Magic\LLM\Message\MultimodalMessage\Text)
                    <p class="whitespace-pre-wrap w-full bg-transparent text-inherit">{{ $content->text }}</p>
                @endif
            @endforeach
        </x-chat.bubble>
        @break
    @default
        <x-chat.bubble class="flex-1 mb-5">
            Unknown message type: {{ $message::class }}
        </x-chat.bubble>
@endswitch
