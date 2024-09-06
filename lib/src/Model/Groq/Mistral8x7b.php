<?php

namespace Capevace\MagicImport\Model\Groq;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Loop\Response\Streamed\ResponseDecoder;
use Capevace\MagicImport\Model\ChatLLM;
use Capevace\MagicImport\Model\Exceptions\RateLimitExceeded;
use Capevace\MagicImport\Model\Exceptions\UnknownInferenceException;
use Capevace\MagicImport\Model\ModelCost;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\MultimodalMessage;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Prompt;
use Closure;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\StreamResponse;

readonly class Mistral8x7b implements ChatLLM
{
    public function __construct(
        public int $maxTokens = 10000,
        public float $temperature = 0.9,
        public ?string $stop = '[end]',
        public float $frequencyPenalty = 0.11,
        public float $presencePenalty = 0.03,
    ) {}

    public function name(): string
    {
        return 'mixtral-8x7b-32768';
    }

    public function parameters(): array
    {
        return [
            'model' => $this->name(),
            'max_tokens' => $this->maxTokens,
            'stop' => $this->stop,
            //            'frequency_penalty' => $this->frequencyPenalty,
            //            'presence_penalty' => $this->presencePenalty,
            //            'response_format' => ['type' => 'json_object']
        ];
    }

    /**
     * @throws RateLimitExceeded
     * @throws UnknownInferenceException
     */
    public function createStreamedHttpRequest(Prompt $prompt): ?StreamResponse
    {
        try {
            return \OpenAI::factory()
                ->withBaseUri('api.groq.com/openai/v1')
                ->withApiKey(config('services.groq.api_key'))
                ->make()
                ->chat()
                ->createStreamed([
                    ...$this->parameters(),
                    'messages' => collect($prompt->messages())
                        // Multimodal messages are not supported by the Groq API, so we need to extract the text content
                        ->map(fn (Message $message) => match ($message::class) {
                            MultimodalMessage::class => new TextMessage(
                                role: $message->role,
                                content: collect($message->content)
                                    ->map(fn (MultimodalMessage\Text|MultimodalMessage\Base64Image $content) => match ($content::class) {
                                        MultimodalMessage\Text::class => $content->text,
                                        default => null
                                    })
                                    ->filter()
                                    ->join("\n\n"),
                            ),
                            default => $message
                        })
                        ->map(fn (Message $message) => $message->toArray())
                        ->toArray(),
                    //                'functions' => array_map(fn(InvokableFunction $function) => $function->schema(), $functions),
                ]);
        } catch (ErrorException $e) {
            if ($e->getErrorCode() === 'rate_limit_exceeded') {
                throw new RateLimitExceeded(
                    message: $e->getMessage(),
                    previous: $e,
                    rateLimits: RateLimitExceeded\RateLimits::parseRateLimitError($e->getMessage()),
                );
            }

            throw new UnknownInferenceException($e->getMessage(), previous: $e);
        }
    }

    public function createHttpRequest(Prompt $prompt): ?CreateResponse
    {
        return \OpenAI::factory()
            ->withBaseUri('api.groq.com/openai/v1')
            ->withApiKey(config('services.groq.api_key'))
            ->make()
            ->chat()
            ->create([
                ...$this->parameters(),
                'messages' => array_map(fn (Message $message) => $message->toArray(), $messages),
                //                'functions' => array_map(fn(InvokableFunction $function) => $function->schema(), $functions),
            ]);
    }

    /**
     * @throws RateLimitExceeded
     * @throws UnknownInferenceException
     */
    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): array
    {
        $response = $this->createStreamedHttpRequest($prompt);

        $decoder = new ResponseDecoder(
            $response,
            onMessageProgress: $onMessageProgress,
            onMessage: $onMessage,
            onTokenStats: $onTokenStats,
            json: true
        );

        return $decoder->process();
    }

    public function send(Prompt $prompt): array {}

    public function cost(): ?ModelCost
    {
        return ModelCost::free();
    }
}
