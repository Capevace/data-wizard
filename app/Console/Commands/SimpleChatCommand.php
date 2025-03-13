<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Mateffy\Magic\LLM\Message\ToolCallMessage;
use Mateffy\Magic\LLM\Message\ToolResultMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\LLM\Models\GeminiFamily;
use Mateffy\Magic\LLM\Models\Gpt4Family;
use Mateffy\Magic;
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
//        $memory = Magic::memory()
//            ->file(path: database_path('memory.json'));

        $chat = Magic::chat()
            ->model(GeminiFamily::flash())
            ->tools([
                /**
                 * @description Test tool
                 * @description $message The message to test
                 */
                'test' => function (string $message) {
                    return [
                        'message' => $message,
                    ];
                }
            ])
            ->system(<<<PROMPT
            You are a personal coding assistant living in a Laravel codebase.
            When outputting text, don't use any formatting, XML, or HTML tags. Just plain text.
            After functions have been called, you can respond to the output, for example with a summary or a follow-up question.
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

            $chat->addMessages($messages->all());

            $text = $messages->lastText();

            $this->info("[LLM]: " . $text);
        }
    }
}
