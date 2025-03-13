<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Mateffy\Magic\Chat\Messages\ToolCallMessage;
use Mateffy\Magic\Chat\Messages\ToolResultMessage;
use Mateffy\Magic\Chat\Messages\Message;
use Mateffy\Magic\Chat\Messages\TextMessage;
use Mateffy\Magic\Models\Anthropic;
use Mateffy\Magic;
use Throwable;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class ChatCommand extends Command
{
    protected $signature = 'chats';

    protected $description = 'Do some LLM chatting';

    public function handle(): void
    {
        $memory = Magic::memory()
            ->file(path: database_path('memory.json'));

        $chat = Magic::chat()
            ->model(Anthropic::sonnet_3_5())
            ->system(<<<PROMPT
            You are a personal coding assistant living in a Laravel codebase.

            You have access to long-term memory that spans AI conversations.
            You may save anything you may need later, its limitless. Especially user preferences or something they mention. It's free to store, so store as much as possible.
            You will probably not be prompted to remember anything!
            So it's up to you to store. When they tell something or voice an interest you should remember it, when they ask a more complex questions you should mark them as interested and much more like this.
            You can call the function "saveMemory", and then still output some text!
            However, you won't need to remember basic stuff like "User initiated conversation. Greeted the assistant.". That's not useful. DO NOT STORE THIS KIND OF CRAP AND ANYTHING SIMILAR, OK!!?? But anything about the user or their interests/tasks/inquiries etc. is.

            You can also always lookup previous memories by searching for them.
            Before responding that you do not know an answer, you should first check if you can find it in your long-term memory.
            It's using vector embeddings, so you can search for general concepts, not just specific answers.
            This memory is always tied to one specific user. So you can assume information stored is connected to the current user.
            If the user asks a question, ALWAYS see if there's something in your memory for this.

            When outputting text, don't use any formatting, XML, or HTML tags. Just plain text.
            PROMPT)
            ->onToolError(fn (Throwable $e) => report($e))
            ->tools([
                'saveMemory' => fn (string $memoryText) => $memory->add($memoryText),
                'findMemory' => fn (string $search) => $memory->search($search),
            ]);

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

            foreach ($messages as $message) {
                $chat->addMessage($message);

                match ($message::class) {
                    TextMessage::class => $this->info("[LLM]: " . str($message->content)->trim("\n")->trim()),
                    ToolCallMessage::class => $this->line("[LLM]: Function call: {$message->call->name}(" . json_encode($message->call->arguments) . ")"),
                    ToolResultMessage::class => $this->line("[LLM]: Function output: " . Str::limit(json_encode($message->output), 100)),
                    default => $this->error('[LLM]: Unknown message type'),
                };
            }
        }
    }
}
