<?php

namespace Mateffy\Magic\LLM\Models\Decoders;

use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\JsonMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\PartialMessage;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\Prompt\Role;
use Mateffy\Magic\Prompt\TokenStats;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mateffy\Magic\Utils\PartialJson;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use OpenAI\Responses\Chat\CreateStreamedResponseChoice;
use OpenAI\Responses\Chat\CreateStreamedResponseDelta;
use OpenAI\Responses\StreamResponse;
use Psr\Http\Message\StreamInterface;

class OpenAiResponseDecoder implements Decoder
{
    public const CHUNK_SIZE = 512;

    /**
     * @var Message[]
     */
    protected array $messages = [];

    public ?int $inputTokens = null;

    public ?int $outputTokens = null;

    public function __construct(
        protected StreamResponse $response,
        /**
         * @var \Closure(PartialMessage): void
         */
        protected ?\Closure $onMessageProgress = null,

        /**
         * @var \Closure(Message): void
         */
        protected ?\Closure $onMessage = null,

        /**
         * @var \Closure(TokenStats): void
         */
        protected ?\Closure $onTokenStats = null,

        /**
         * @var \Closure(TokenStats): void
         */
        protected ?\Closure $onEnd = null,

        /**
         * @var bool
         */
        protected bool $json = true,
    ) {}

    public function process(): array
    {
        $unfinished = null;

        /** @var ?PartialMessage $message */
        $message = null;

        $pushMessage = function () use (&$message) {
            $this->messages[] = $message;

            if ($this->onMessage) {
                ($this->onMessage)($message);
            }

            $message = null;
        };

        /** @var CreateStreamedResponseChoice[] $lastResponse */
        $choices = [];

        foreach($this->response as $response){
            /**
             * @var CreateStreamedResponse $response
             */
            foreach ($response->choices as $choice) {
                $delta = $choice->delta;
                $choices[] = $delta;

                // End the previous message, if one exists
//                if ($delta->role !== null && $message !== null) {
//                    $pushMessage();
//                }

                if ($delta->role !== null && $message === null) {
                    $toolCall = $delta->toolCalls[0] ?? null;

                    $message = $toolCall !== null
                        ? new FunctionInvocationMessage(
                            role: Role::Assistant,
                            call: new FunctionCall(
                                name: $toolCall->function->name,
                                arguments: PartialJson::parse($toolCall->function->arguments) ?? [],
                                id: $toolCall->id,
                            ),
                            partial: $toolCall->function->arguments ?? ''
                        )
                        : TextMessage::fromChunk($delta->content ?? '');
                } elseif ($message && $delta->content !== null) {
                    $message->append($delta->content);

                    if ($this->onMessageProgress) {
                        ($this->onMessageProgress)($message);
                    }
                } else if ($message instanceof FunctionInvocationMessage && $toolCall = $delta->toolCalls[0] ?? null) {
                    $message->append($toolCall->function->arguments);

                    if ($this->onMessageProgress) {
                        ($this->onMessageProgress)($message);
                    }
                }
            }
        }

        if ($message !== null) {
            $pushMessage();
        }

        return $this->messages;
    }
}
