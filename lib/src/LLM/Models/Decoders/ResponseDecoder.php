<?php

namespace Mateffy\Magic\LLM\Models\Decoders;

use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\JsonMessage;
use Mateffy\Magic\LLM\Message\PartialMessage;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\Prompt\Role;
use Illuminate\Support\Arr;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\StreamResponse;

class ResponseDecoder implements Decoder
{
    protected array $messages = [];

    protected ?PartialMessage $currentPartial = null;

    public function __construct(
        protected CreateResponse|StreamResponse $response,
        protected ?\Closure $onMessageProgress = null,
        protected ?\Closure $onMessage = null,
        protected ?\Closure $onTokenStats = null,
        protected bool $json = true,
    ) {}

    public function process(): array
    {
        if ($this->response instanceof CreateResponse) {
            foreach ($this->response->choices as $choice) {
                $this->processRawMessage($choice->message->toArray());
            }
        } elseif ($this->response instanceof StreamResponse) {
            foreach ($this->response as $stream) {
                $this->processRawMessage($stream->choices[0]->delta->toArray());
            }
        }

        if ($this->currentPartial !== null) {
            $this->messages[] = $this->currentPartial->toResponse();

            $this->onMessage?->call($this, $this->currentPartial->toResponse()->toMessage());
        }

        return $this->messages;
    }

    public function processRawMessage(array $message)
    {
        $role = Role::tryFrom(Arr::get($message, 'role', ''));

        dump([$this->currentPartial, $role]);

        if ($this->currentPartial === null) {
            $this->currentPartial = $this->beginNewMessage($message);

            if ($this->currentPartial !== null) {
                $this->onMessageProgress?->call($this, $this->currentPartial);
            }
        } elseif ($role !== null && $role !== $this->currentPartial->role()) { // Role being set signals the beginning of a new message
            // If it is the beginning of a new message and we have a current message, we 'send off' the current message
            $this->messages[] = $this->currentPartial;

            $this->onMessage?->call($this, $this->currentPartial);

            $this->currentPartial = $this->beginNewMessage($message);

            if ($this->currentPartial !== null) {
                $this->onMessageProgress?->call($this, $this->currentPartial);
            }
        } else {
            if ($tool = Arr::get($message, 'tool_calls.0.function', null)) {
                $this->currentPartial = $this->currentPartial->append($tool['arguments']);
            } elseif ($text = $message['content'] ?? $message['text'] ?? null) {
                $this->currentPartial = $this->currentPartial->append($text);
            }

            $this->onMessageProgress?->call($this, $this->currentPartial);
        }

        //        if ($this->currentPartial instanceof PartialTextResponse)  {
        //            $manualFunctionPartial = $this->tryManualFunctionResponse($this->currentPartial->content());
        //
        //            if ($manualFunctionPartial) {
        //                $this->$this->messages = [
        //                    ...$this->$this->messages,
        //                    ...array_map(fn(PartialResponse $response) => $response->toResponse(), $manualFunctionPartial)
        //                ];
        //
        //                $this->currentPartial = null;
        //            }
        //        }
    }

    public function beginNewMessage(array $message): ?PartialMessage
    {
        $role = Role::tryFrom(Arr::get($message, 'role', '')) ?? Role::Assistant;
        $content = Arr::get($message, 'content', '');
        $function = Arr::get($message, 'tool_calls.0.function');

        if ($role && empty($content) && empty($function)) {
            return null;
        }

        if ($function !== null) {
            return new FunctionInvocationMessage(
                role: $role,
                call: FunctionCall::tryFrom($function),
                partial: $content,
            );
        }

        return $this->json
            ? JsonMessage::fromChunk($content)
            : TextMessage::fromChunk($content);
    }
}
