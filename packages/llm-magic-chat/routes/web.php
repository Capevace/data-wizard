<?php

use Illuminate\Support\Facades\Route;
use Mateffy\Magic\Chat\HasChat;
use Mateffy\Magic\Chat\Livewire\StreamableMessage;
use Mateffy\Magic\Chat\ToolWidget;
use Mateffy\Magic\Functions\MagicFunction;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\Message;

Route::get('/poll/{chat}/{conversationId}', function (string $chat, string $conversationId) {
    if (!class_exists($chat) || !class_implements($chat, HasChat::class)) {
        abort(404);
    }

    /** @var class-string<HasChat> $chat */

    $messages = StreamableMessage::getStreamedMessages($conversationId);

    \Illuminate\Support\Facades\Log::info($conversationId, [
        'messages' => $messages->all(),
    ]);

    return response()->json([
        'messages' => $messages
            ->map(function (Message $message) use ($chat, $messages) {
                $result = $chat::renderChatMessage(message:$message);

                if ($result instanceof ToolWidget && $message instanceof FunctionInvocationMessage) {
                    return $result->render(
                        tool: new MagicFunction(
                            name: $message->call->name,
                            schema: $message->schema ?? [],
                            callback: fn () => null, // Never called, as we're only rendering here
                        ),
                        invocation: $message,
                        output: $messages->firstFunctionOutput(fn (FunctionOutputMessage $output) => $message->call->id && $output->call->id === $message->call->id),
                    );
                } else if ($result instanceof ToolWidget) {
                    report(new \Exception('ToolWidget without invocation: ' . json_encode(['message' => $message, 'result' => $result])));

                    return null;
                }

                return $result;
            })
            ->values()
            ->all(),
    ]);
})
    ->middleware(['signed'])
    ->name('chat.poll');
