<?php

namespace Mateffy\Magic\LLM\Models\Apis;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Exceptions\InvalidRequest;
use Mateffy\Magic\LLM\Exceptions\TooManyTokensForModelRequested;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\JsonMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;
use Mateffy\Magic\LLM\Message\MultimodalMessage\ContentInterface;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Text;
use Mateffy\Magic\LLM\Message\MultimodalMessage\ToolResult;
use Mateffy\Magic\LLM\Message\MultimodalMessage\ToolUse;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\MessageCollection;
use Mateffy\Magic\LLM\Models\Decoders\ClaudeResponseDecoder;
use Mateffy\Magic\Prompt\Prompt;
use Mateffy\Magic\Prompt\Role;
use Mateffy\Magic\Prompt\TokenStats;
use OpenAI\Responses\Chat\CreateResponse;
use Psr\Http\Message\StreamInterface;

trait UsesAnthropicApi
{
    protected function getApiToken(): string
    {
        return config('magic-extract.apis.anthropic.token');
    }

    /**
     * @throws InvalidRequest
     * @throws TooManyTokensForModelRequested
     */
    protected function createStreamedHttpRequest(Prompt $prompt): \Psr\Http\Message\StreamInterface
    {
        $client = new Client([
            'base_uri' => 'https://api.anthropic.com',
            'headers' => [
                'anthropic-version' => '2023-06-01',
                'anthropic-beta' => 'messages-2023-12-15',
                'content-type' => 'application/json',
                'x-api-key' => $this->getApiToken(),
            ],
        ]);

        $otherMessages = collect($prompt->messages())
            ->filter(fn (Message $message) => $message->role !== Role::System);

        $options = $this->getOptions();
        $data = [
            'model' => $this->getModelName(),
            'max_tokens' => $options->maxTokens,
            'system' => $prompt->system(),
            'messages' => collect($otherMessages)
                ->map(fn (Message $message) => match ($message::class) {
                    TextMessage::class => [
                        'role' => $message->role,
                        'content' => $message->content,
                    ],

                    JsonMessage::class => [
                        'role' => $message->role,
                        'content' => json_encode($message->data),
                    ],

//                    {"role": "assistant", "content": [
//                        {"type": "tool_use", "id": "toolu_01A09q90qw90lq917835lq9", "name": "get_weather", "input": {"location": "San Francisco, CA"}}
//                    ]},
                    FunctionInvocationMessage::class => [
                        'role' => $message->role,
                        'content' => [
                            [
                                'type' => 'tool_use',
                                'id' => $message->call->id ?? $message->call->name,
                                'name' => $message->call->name,
                                'input' => $message->call->arguments,
                            ],
                        ],
                    ],

//                    {"role": "user", "content": [
//                        {"type": "tool_result", "tool_use_id": "toolu_01A09q90qw90lq917835lq9", "content": "15 degrees"}
//                    ]},
                    FunctionOutputMessage::class => [
                        'role' => $message->role,
                        'content' => [
                            [
                                'type' => 'tool_result',
                                'tool_use_id' => $message->call->id ?? $message->call->name,
                                'content' => [
                                    ['type' => 'text', 'text' => $message->text()],
                                ]
                            ],
                        ],
                    ],

                    MultimodalMessage::class => [
                        'role' => $message->role,
                        'content' => collect($message->content)
                            ->map(fn (ContentInterface $message) => match ($message::class) {
                                Text::class => [
                                    'type' => 'text',
                                    'text' => $message->text,
                                ],
                                Base64Image::class => [
                                    'type' => 'image',
                                    'source' => [
                                        'type' => 'base64',
                                        'media_type' => $message->mime,
                                        'data' => $message->imageBase64,
                                    ]
                                ],
                                ToolUse::class => [
                                    'type' => 'tool_use',
                                    'id' => $message->call->id,
                                    'name' => $message->call->name,
                                    'input' => $message->call->arguments,
                                ],
                                ToolResult::class => [
                                    'type' => 'tool_result',
                                    'tool_use_id' => $message->call->id,
                                    'content' => json_encode($message->output),
                                ],
                            })
                            ->toArray(),
                    ],

                    default => null,
                })
//                ->dd()
                ->filter()
                ->values()
                ->toArray(),
            'stream' => true,
            'tools' => collect($prompt->functions())
                ->map(fn (InvokableFunction $function) => [
                    'name' => $function->name(),
                    'description' => method_exists($function, 'description') ? $function->description() : '',
                    'input_schema' => $function->schema(),
                ]),
        ];

        if (method_exists($prompt, 'forceFunction') && ($fn = $prompt->forceFunction())) {
            $data['tool_choice'] = ['type' => 'tool', 'name' => $fn->name()];
        }

        try {
            $request = new Request('POST', '/v1/messages', [], json_encode($data));
            $response = $client->send($request, ['stream' => true]);

            return $response->getBody();
        } catch (ClientException $e) {
            $json = $e->getResponse()->getBody();
            $data = json_decode($json, true);

            if (Arr::has($data, 'error')) {
                $type = Arr::get($data, 'error.type');

                if ($type === 'invalid_request_error' && Str::startsWith(Arr::get($data, 'error.message'), 'max_tokens')) {
                    throw new TooManyTokensForModelRequested('Too many tokens: '.Arr::get($data, 'error.message'), previous: $e);
                }

                if ($message = Arr::get($data, 'error.message')) {
                    throw new InvalidRequest('API error', $message, previous: $e);
                }
            }

            throw new InvalidRequest('API error', $e->getMessage(), previous: $e);
        } catch (GuzzleException $e) {
            throw new InvalidRequest('HTTP error', $e->getMessage(), previous: $e);
        }
    }

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): MessageCollection
    {
        $stream = $this->createStreamedHttpRequest($prompt);

        $cost = $this->getModelCost();

        $decoder = new ClaudeResponseDecoder(
            $stream,
            $onMessageProgress,
            $onMessage,
            onTokenStats: fn (TokenStats $stats) => $onTokenStats
                ? $onTokenStats($cost
                    ? $stats->withCost($cost)
                    : $stats
                )
                : $stats,
            json: $prompt->shouldParseJson()
        );

        return MessageCollection::make($decoder->process());
    }

    protected function createHttpRequest(Prompt $prompt): StreamInterface|CreateResponse|null
    {
        throw new \Exception('Not implemented');
    }

    public function send(Prompt $prompt): MessageCollection
    {
        throw new \Exception('Not implemented');
    }
}
