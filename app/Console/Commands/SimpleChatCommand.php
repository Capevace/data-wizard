<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\Magic;
use Throwable;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class SimpleChatCommand extends Command
{
    protected $signature = 'chat';

    protected $description = 'Do some LLM chatting';

    public function handle(): void
    {
        $memory = Magic::memory()
            ->file(path: database_path('memory.json'));

        $chat = Magic::chat()
            ->model(Claude3Family::sonnet_3_5())
            ->system(<<<PROMPT
            You are a personal coding assistant living in a Laravel codebase.
            When outputting text, don't use any formatting, XML, or HTML tags. Just plain text.
            PROMPT);

        while (true) {
            $message = text(
                label: 'Message',
                placeholder: 'E.g. Hello, how are you?',
            );

            $chat->addMessage(TextMessage::user($message));

            $messages = spin(
                callback: fn () => $chat->stream(),
                message: 'Thinking...'
            );

            $text= $messages->firstText();

            $chat->addMessage(TextMessage::assistant($text));
            $this->info("[LLM]: " . $text);
        }
    }
}
