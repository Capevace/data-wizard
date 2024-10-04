<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\MessageCollection;

class StreamableMessage extends Component
{
    #[Locked]
    public string $view = 'livewire.streamable-message';

    #[Locked]
    public string $conversationId;

    #[Locked]
    public string $chat;

    #[Locked]
    public ?string $poll = null;

    #[On('startPolling')]
    public function startPolling(): void
    {
        $this->poll = $this->getPollUrl();
    }

    #[On('stopPolling')]
    public function stopPolling(): void
    {
        $this->poll = null;

        StreamableMessage::clear($this->conversationId);
    }

    protected function getPollUrl(): string
    {
        return URL::temporarySignedRoute(
            name: 'chat.poll',
            expiration: now()->addMinutes(10),
            parameters: ['conversationId' => $this->conversationId, 'chat' => $this->chat],
        );
    }

    public static function composeCacheKey(string $conversationId): string
    {
        return "{$conversationId}_streamed";
    }

    public static function getStreamedMessages(string $conversationId): MessageCollection
    {
        $messages = cache()->get(self::composeCacheKey($conversationId));

        if ($messages === null || count($messages) === 0) {
            return MessageCollection::make();
        }

        $parsed = collect($messages)
            ->map(function (array $message) {
                /** @var class-string<Message> $type */
                $type = $message['type'];
                $data = $message['data'] ?? [];

                try {
                    return $type::fromArray($data);
                } catch (\Throwable $e) {
                    report($e);
                    return null;
                }
            })
            ->filter()
            ->values();

        return MessageCollection::make($parsed);
    }

    public static function put(string $conversationId, array $messages): void
    {
        $data = collect($messages)
            ->filter()
            ->map(fn (Message $message) => ['type' => $message::class, 'data' => $message->toArray()])
            ->values()
            ->all();

        cache()->put(StreamableMessage::composeCacheKey($conversationId), $data, now()->addMinutes(5));
    }

    public static function clear(string $conversationId): void
    {
        cache()->forget(StreamableMessage::composeCacheKey($conversationId));
    }
}
