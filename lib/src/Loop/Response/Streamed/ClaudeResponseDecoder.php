<?php

namespace Capevace\MagicImport\Loop\Response\Streamed;

use Capevace\MagicImport\Prompt\Role;
use Capevace\MagicImport\Prompt\TokenStats;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\Chat\CreateStreamedResponse;
use OpenAI\Responses\StreamResponse;
use Psr\Http\Message\StreamInterface;

class ClaudeResponseDecoder implements Decoder
{
    public const CHUNK_SIZE = 512;

    protected bool $forceNewMessage = false;

    protected array $responses = [];

    protected ?PartialResponse $currentPartial = null;

    public ?int $inputTokens = null;
    public ?int $outputTokens = null;

    public function __construct(
        protected StreamInterface $response,
        protected ?\Closure $onMessageProgress = null,
        protected ?\Closure $onMessage = null,
        protected ?\Closure $onTokenStats = null,
        protected ?\Closure $onEnd = null,
        protected bool $json = true,
    )
    {

    }

    public function process(): array
    {
        $unfinished = null;

        $messages = collect();

        /** @var PartialResponse|null $message */
        $message = null;

        while (!$this->response->eof()) {
            $output = $this->response->read(self::CHUNK_SIZE);

            if ($unfinished !== null) {
                $output = $unfinished . $output;
                $unfinished = null;
            }

            $newEvents = str($output)
                ->explode("\n\n");

            $lastEvent = $newEvents->pop();

            if (!Str::endsWith($lastEvent, "\n\n")) {
                $unfinished = $lastEvent;
            } else {
                $newEvents->push($lastEvent);
            }

            $newParsedEvents = collect();

            foreach ($newEvents as $event) {
                [$event, $data] = str($event)
                    ->explode("\n");

                $event = Str::after($event, 'event: ');
                $data = json_decode(Str::after($data, 'data: '), true);

                $newParsedEvents->push([$event, $data]);

                // End the previous message, if one exists
                if ($event === 'message_start' && $message !== null) {
                    $messages->push($message);

                    $response = $message->toResponse();
                    $this->responses[] = $response;

                    if ($this->onMessage) {
                        ($this->onMessage)($response->toMessage());
                    }

                    $message = null;
                }

                if ($event === 'message_start') {
                    $message = $this->json
                        ? new PartialJsonResponse(Role::Assistant, '')
                        : new PartialTextResponse(Role::Assistant, '');


                    $this->inputTokens = intval(Arr::get($data, 'message.usage.input_tokens') ?? 0);
                    $this->outputTokens = intval(Arr::get($data, 'message.usage.output_tokens') ?? 0);

                    if ($this->onTokenStats) {
                        ($this->onTokenStats)(TokenStats::withInputAndOutput($this->inputTokens, $this->outputTokens));
                    }
                } elseif ($event === 'content_block_start') {
                    $part = match ($data['content_block']['type']) {
                        default => $data['content_block']['text'],
                        'tool_use' => null,
                    };

                    if ($part) {
                        $message = $message->append($part);
                    }

                    if ($this->onMessageProgress) {
                        ($this->onMessageProgress)($message->toResponse()->toMessage());
                    }
                } elseif ($event === 'content_block_delta') {
                    $part = match ($data['delta']['type']) {
                        default => $data['delta']['text'],
                        'input_json_delta' => $data['delta']['partial_json'],
                    };

                    $message = $message->append($part);

                    if ($this->onMessageProgress) {
                        ($this->onMessageProgress)($message->toResponse()->toMessage());
                    }
                } elseif ($event === 'message_delta') {
                    // Anthropic seems to be using both 'delta' and 'usage' keys for the same thing
                    $this->outputTokens = intval(Arr::get($data, 'delta.usage.output_tokens') ?? Arr::get($data, 'usage.output_tokens'));

                    if ($this->onTokenStats) {
                        ($this->onTokenStats)(TokenStats::withInputAndOutput($this->inputTokens, $this->outputTokens));
                    }

                    if ($message !== null) {
                        $messages->push($message);

                        $response = $message->toResponse();
                        $this->responses[] = $response;

                        if ($this->onMessage) {
                            ($this->onMessage)($response->toMessage());
                        }

                        $message = null;
                    }
                }
            }
        }

        if ($message !== null) {
            $messages->push($message);

            $response = $message->toResponse();
            $this->responses[] = $response;

            if ($this->onMessage) {
                ($this->onMessage)($response);
            }
        }

        return $this->responses;
    }
}
