<?php

namespace Mateffy\Magic\LLM\Models\Apis;

use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Exceptions\InvalidRequest;
use Mateffy\Magic\LLM\Exceptions\TooManyTokensForModelRequested;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\Models\Decoders\ClaudeResponseDecoder;
use Mateffy\Magic\Prompt\Prompt;
use Mateffy\Magic\Prompt\Role;
use Mateffy\Magic\Prompt\TokenStats;
use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
        // Prepare logical messages to Claude API messages
        $serializedMessages = collect($prompt->messages())
            ->map(fn (Message $message) => match ($message::class) {
                FunctionOutputMessage::class => new TextMessage(role: Role::User, content: 'OUTPUT: '.json_encode($message->output, JSON_THROW_ON_ERROR)),
                default => $message
            });

        $client = new Client([
            'base_uri' => 'https://api.anthropic.com',
            'headers' => [
                'anthropic-version' => '2023-06-01',
                'anthropic-beta' => 'messages-2023-12-15',
                'content-type' => 'application/json',
                'x-api-key' => $this->getApiToken(),
            ],
        ]);

        $otherMessages = $serializedMessages->filter(fn (Message $message) => $message->role !== Role::System);

        $options = $this->getOptions();
        $data = [
            'model' => $this->getModelName(),
            'max_tokens' => $options->maxTokens,
            'system' => $prompt->system(),
            'messages' => collect($otherMessages)
                ->map(fn (Message $message) => $message->toArray())
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

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): array
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
                : $stats
        );

        return $decoder->process();
    }

    protected function createHttpRequest(Prompt $prompt): StreamInterface|CreateResponse|null
    {
        throw new \Exception('Not implemented');
    }

    public function send(Prompt $prompt): array
    {
        throw new \Exception('Not implemented');
    }
}
