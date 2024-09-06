<?php

namespace Capevace\MagicImport\Loop\Response\Streamed;

use Capevace\MagicImport\Prompt\Role;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\StreamResponse;

class ResponseDecoder implements Decoder
{
    protected bool $forceNewMessage = false;

    protected array $responses = [];

    protected ?PartialResponse $currentPartial = null;

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
                dump($stream->choices[0]->delta->toArray());
                $this->processRawMessage($stream->choices[0]->delta->toArray());
            }
        }

        if ($this->currentPartial !== null) {
            $this->responses[] = $this->currentPartial->toResponse();

            $this->onMessage?->call($this, $this->currentPartial->toResponse()->toMessage());
        }

        return $this->responses;
    }

    public function processRawMessage(array $message)
    {
        $role = Role::tryFrom(Arr::get($message, 'role', ''));

        dump([$this->currentPartial, $role]);

        if ($this->currentPartial === null) {
            $this->currentPartial = $this->beginNewMessage($message);

            if ($this->currentPartial !== null) {
                $this->onMessageProgress?->call($this, $this->currentPartial->toResponse()->toMessage());
            }
        } elseif ($role !== null && $role !== $this->currentPartial->role()) { // Role being set signals the beginning of a new message
            // If it is the beginning of a new message and we have a current message, we 'send off' the current message
            $this->responses[] = $this->currentPartial->toResponse();

            $this->onMessage?->call($this, $this->currentPartial->toResponse()->toMessage());

            $this->currentPartial = $this->beginNewMessage($message);

            if ($this->currentPartial !== null) {
                $this->onMessageProgress?->call($this, $this->currentPartial->toResponse()->toMessage());
            }
        } else {
            if ($tool = Arr::get($message, 'tool_calls.0.function', null)) {
                $this->currentPartial = $this->currentPartial->append($tool['arguments']);
            } elseif ($text = $message['content'] ?? $message['text'] ?? null) {
                $this->currentPartial = $this->currentPartial->append($text);
            }

            $this->onMessageProgress?->call($this, $this->currentPartial->toResponse()->toMessage());
        }

        //        if ($this->currentPartial instanceof PartialTextResponse)  {
        //            $manualFunctionPartial = $this->tryManualFunctionResponse($this->currentPartial->content());
        //
        //            if ($manualFunctionPartial) {
        //                $this->responses = [
        //                    ...$this->responses,
        //                    ...array_map(fn(PartialResponse $response) => $response->toResponse(), $manualFunctionPartial)
        //                ];
        //
        //                $this->currentPartial = null;
        //            }
        //        }
    }

    public function beginNewMessage(array $message): ?PartialResponse
    {
        $role = Role::tryFrom(Arr::get($message, 'role', '')) ?? Role::Assistant;
        $content = Arr::get($message, 'content', '');
        $function = Arr::get($message, 'tool_calls.0.function');

        if ($role && empty($content) && empty($function)) {
            return null;
        }

        if ($function !== null) {
            return new PartialFunctionCallResponse(
                role: $role,
                name: $function['name'],
                arguments: $function['arguments']
            );
        }

        return $this->json
            ? new PartialJsonResponse(
                role: $role,
                content: $content
            )
            : new PartialTextResponse(
                role: $role,
                content: $content
            );
    }

    public function tryManualFunctionResponse(string $content): array
    {
        try {
            $responses = json_decode($content, true, flags: JSON_THROW_ON_ERROR);

            $validator = validator($responses, [
                '*.text' => 'nullable|string',
                '*.tool_calls' => 'nullable|string',
                '*.arguments' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                dd($validator->errors());

                return [];
            }

            $responses = collect($responses)
                ->map(function (mixed $response) {
                    if (! is_array($response)) {
                        return null;
                    }

                    if (Arr::get($response, 'tool_calls')) {
                        $arguments = Arr::get($response, 'arguments', '');

                        if (! is_string($arguments)) {
                            $arguments = json_encode($arguments) ?? '';
                        }

                        return new PartialFunctionCallResponse(
                            role: Role::Assistant,
                            name: $response['tool_calls'],
                            arguments: $arguments
                        );
                    } elseif ($text = $response['text']) {
                        return new PartialTextResponse(
                            role: Role::Assistant,
                            content: $text
                        );
                    }

                    return null;
                })
                ->filter()
                ->all();

            if (! empty($responses)) {
                return $responses;
            }
        } catch (Exception $e) {
            return [];

        }

        $content = Str::before($content, "\n");

        if ($response = json_decode($content, true)) {
            if (is_array($response) && Arr::get($response, 'tool_calls')) {
                $arguments = Arr::get($response, 'arguments', '');

                if (! is_string($arguments)) {
                    $arguments = json_encode($arguments) ?? '';
                }

                return [new PartialFunctionCallResponse(
                    role: Role::Assistant,
                    name: $response['tool_calls'],
                    arguments: $arguments
                )];
            }
        }

        return [];
    }
}
