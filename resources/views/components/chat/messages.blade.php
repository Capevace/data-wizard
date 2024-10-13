@props([
    'messages' => method_exists($this, 'getChatMessages') ? $this->getChatMessages() : [],
    'chat' => $this instanceof \App\Livewire\Components\Concerns\HasChat
        ? get_class($this)
        : null,
    'renderMessage' => fn (\Mateffy\Magic\LLM\Message\Message $message) => $this instanceof \App\Livewire\Components\Concerns\HasChat
        ? self::renderChatMessage($message)
        : null,
])

<div
    {{ $attributes->class('') }}
>
    @foreach ($messages as $message)
        <div
            @class([
                'flex',
                'justify-start' => $message->role === \Mateffy\Magic\Prompt\Role::Assistant,
                'justify-end' => $message->role === \Mateffy\Magic\Prompt\Role::User,
            ])

            wire:key="{{ md5($message->text()) }}"
        >
            {!! $renderMessage($message) !!}
        </div>
    @endforeach

    <livewire:streamable-message
        :conversation-id="$this->conversationId"
        :chat="$chat"
        key="streamable-message"
    />
</div>
