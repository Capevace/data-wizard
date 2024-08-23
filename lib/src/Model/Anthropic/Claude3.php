<?php

namespace Capevace\MagicImport\Model\Anthropic;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Loop\Response\Streamed\ClaudeResponseDecoder;
use Capevace\MagicImport\Loop\Response\Streamed\Decoder;
use Capevace\MagicImport\Model\ChatLLM;
use Capevace\MagicImport\Model\Exceptions\InvalidRequest;
use Capevace\MagicImport\Model\Exceptions\TooManyTokensForModelRequested;
use Capevace\MagicImport\Prompt\Message\FunctionOutputMessage;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Prompt;
use Capevace\MagicImport\Prompt\Role;
use Capevace\MagicImport\Prompt\TokenStats;
use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use \OpenAI\Responses\StreamResponse;
use Psr\Http\Message\StreamInterface;

abstract readonly class Claude3 implements ChatLLM
{
    public function __construct(
        public int $maxTokens = 1200,
        public float $temperature = 0.83,
        public ?string $stop = null,
        public float $frequencyPenalty = 0.11,
        public float $presencePenalty = 0.03,
    )
    {
    }

    abstract public function name(): string;

    public function parameters(): array
    {
        return [
            'model' => $this->name(),
            'max_tokens' => $this->maxTokens,
//            'stop' => $this->stop,
//            'frequency_penalty' => $this->frequencyPenalty,
//            'presence_penalty' => $this->presencePenalty,
        ];
    }

    /**
     * @throws InvalidRequest
     * @throws TooManyTokensForModelRequested
     */
    protected function createStreamedHttpRequest(Prompt $prompt): \Psr\Http\Message\StreamInterface
    {
        // Prepare logical messages to Claude API messages
        $serializedMessages = collect($prompt->messages())
            ->map(fn(Message $message) => match ($message::class) {
                FunctionOutputMessage::class => new TextMessage(role: Role::User, content: 'OUTPUT: ' . json_encode($message->output, JSON_THROW_ON_ERROR)),
                default => $message
            });

        $apiKey = getenv('ANTHROPIC_API_KEY');

        $client = new Client([
            'base_uri' => 'https://api.anthropic.com',
            'headers' => [
                'anthropic-version' => '2023-06-01',
                'anthropic-beta' => 'messages-2023-12-15',
                'content-type' => 'application/json',
                'x-api-key' => $apiKey,
            ],
        ]);

        $systemMessages = $serializedMessages->filter(fn(Message $message) => $message->role === Role::System);
        $otherMessages = $serializedMessages->filter(fn(Message $message) => $message->role !== Role::System);

        $system = $systemMessages->reduce(fn (string $acc, TextMessage $message) => $message->content . "\n\n", '');

        $data = [
            ...$this->parameters(),
            'system' => $system,
            'messages' => collect($otherMessages)
                ->map(fn (Message $message) => $message->toArray())
                ->values()
                ->toArray(),
            'stream' => true,
        ];

        $request = new Request('POST', '/v1/messages', [], json_encode($data));

        try {
            $response = $client->send($request, ['stream' => true]);

            return $response->getBody();
        } catch (ClientException $e) {
            $json = $e->getResponse()->getBody();
            $data = json_decode($json, true);

            if (Arr::has($data, 'error')) {
                $type = Arr::get($data, 'error.type');

                if ($type === 'invalid_request_error' && Str::startsWith(Arr::get($data, 'error.message'), 'max_tokens')) {
                    throw new TooManyTokensForModelRequested(Arr::get($data, 'error.message'), previous: $e);
                }
            }

            throw new InvalidRequest($e->getMessage(), previous: $e);
        } catch (GuzzleException $e) {
            throw new InvalidRequest($e->getMessage(), previous: $e);
        }
    }

    protected function createHttpRequest(Prompt $prompt): StreamInterface|CreateResponse|null
    {
        throw new \Exception('Not implemented');
    }

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): array
    {
        $stream = $this->createStreamedHttpRequest($prompt);

        $cost = $this->cost();
        $decoder = new ClaudeResponseDecoder(
            $stream,
            $onMessageProgress,
            $onMessage,
            onTokenStats: fn (TokenStats $stats) => $onTokenStats($stats->withCost($cost))
        );

        return $decoder->process();
    }

    public function send(Prompt $prompt): array
    {
        throw new \Exception('Not implemented');
    }
}
