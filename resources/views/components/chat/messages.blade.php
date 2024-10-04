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
    {{ $attributes }}
>
    @foreach ($messages as $message)
        <div class="flex items-start" wire:key="{{ md5($message->text()) }}">
            {!! $renderMessage($message) !!}
        </div>
    @endforeach

    <livewire:streamable-message
        :conversation-id="$this->conversationId"
        :chat="$chat"
        key="streamable-message"
    />
</div>
