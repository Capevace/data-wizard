@props([
    'messages' => method_exists($this, 'getChatMessages') ? $this->getChatMessages() : [],
    'chat' => $this instanceof \Mateffy\Magic\Chat\HasChat
        ? get_class($this)
        : null,
    'renderMessage' => fn (\Mateffy\Magic\LLM\Message\Message $message) => $this->renderMessage($message)
])

<div
    {{ $attributes->class('flex flex-col gap-2 llm-magic-messages') }}
>
    <style>
        .llm-magic-messages .fi-global-search-results-ctn {
            position: relative !important;
            max-width: 70% !important;
            min-width: 50%;
            display: block !important;
        }

        .llm-magic-messages .fi-global-search-field {
            width: fit-content;
        }
    </style>
    @foreach ($messages as $message)
        <div
            @class([
                'flex',
                'justify-start' => $message->role === \Mateffy\Magic\Prompt\Role::Assistant,
                'justify-end' => $message->role === \Mateffy\Magic\Prompt\Role::User,
            ])
            data-magic-id="{{ md5($message->text()) }}"
            wire:key="{{ md5($message->text()) }}"
        >
            {!! $renderMessage($message) !!}
        </div>
    @endforeach

    <livewire:llm-magic.streamable-message
        :conversation-id="$this->conversationId"
        :chat="$chat"
        key="streamable-message-{{ $this->conversationId }}"
    />
</div>
