@props([
    /** @var \Mateffy\Magic\LLM\Message\Message $message */
    'message',

    'streaming' => false,
    'isSecondLast' => false,
    'isCurrent' => false,
    'plain' => false,
])

<?php
$bubble = $plain ? 'chat.plain-bubble' : 'chat.bubble';
?>

@switch ($message::class)
    @case(\Mateffy\Magic\LLM\Message\TextMessage::class)
        <x-dynamic-component :component="$bubble" @class(['mb-8 mt-2 max-w-lg w-fit'])>
            <p class="whitespace-pre-wrap w-full bg-transparent text-inherit font-sans">{{ trim($message->text()) }}</p>
        </x-dynamic-component>
        @break
    @case(\Mateffy\Magic\LLM\Message\MultimodalMessage::class)
        <x-dynamic-component :component="$bubble" class="mb-8 mt-2 max-w-lg w-fit">
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
        </x-dynamic-component>
        @break
    @default
        <x-dynamic-component :component="$bubble" class="flex-1 mb-8 mt-2">
            Unknown message type: {{ $message::class }}
        </x-dynamic-component>
@endswitch
