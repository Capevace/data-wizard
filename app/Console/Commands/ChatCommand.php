<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class ChatCommand extends Command
{
    protected $signature = 'chat';

    protected $description = 'Do some LLM chatting';

    public function handle(): void
    {
        $quit = false;
        $chat = Magic::chat()
            ->model(Claude3Family::haiku());

        while (true) {
            $message = text(
                label: 'Message',
                placeholder: 'E.g. Hello, how are you?',
            );

            $chat->addMessage(TextMessage::user($message));

            $response = spin(
                message: 'Thinking...',
                callback: fn () => $chat->stream()->firstText()
            );

            $chat->addMessage(TextMessage::assistant($response));

            $this->info("[LLM]: {$response}");
        }
    }
}
