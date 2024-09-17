<?php

namespace Capevace\MagicImport\LLM\Models\Apis;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\LLM\Exceptions\RateLimitExceeded;
use Capevace\MagicImport\LLM\Exceptions\UnknownInferenceException;
use Capevace\MagicImport\LLM\Message\Message;
use Capevace\MagicImport\LLM\Message\MultimodalMessage;
use Capevace\MagicImport\LLM\Message\TextMessage;
use Capevace\MagicImport\LLM\ModelCost;
use Capevace\MagicImport\LLM\Models\Decoders\ResponseDecoder;
use Capevace\MagicImport\Prompt\Prompt;
use Capevace\MagicImport\Prompt\Role;
use Closure;
use ErrorException;
use OpenAI\Responses\StreamResponse;

trait UsesGroqApi
{
    protected function getApiToken(): string
    {
        return config('magic-extract.apis.groq.token');
    }

    protected function parameters(): array
    {
        return [
            'model' => $this->getModelName(),
            'max_tokens' => $this->options->maxTokens,
            //            'stop' => $this
            //            'frequency_penalty' => $this->frequencyPenalty,
            //            'presence_penalty' => $this->presencePenalty,
            //            'response_format' => ['type' => 'json_object']
        ];
    }

    /**
     * @throws RateLimitExceeded
     * @throws UnknownInferenceException
     */
    protected function createStreamedHttpRequest(Prompt $prompt): ?StreamResponse
    {
        try {
            return \OpenAI::factory()
                ->withBaseUri('api.groq.com/openai/v1')
                ->withApiKey($this->getApiToken())
                ->make()
                ->chat()
                ->createStreamed([
                    ...$this->parameters(),
                    'tool_choice' => method_exists($prompt, 'forceFunction') && ($fn = $prompt->forceFunction())
                        ? [
                            'type' => 'function',
                            'function' => [
                                'name' => $fn->name(),
                            ],
                        ]
                        : null,
                    'tools' => collect($prompt->functions())
                        ->map(fn (InvokableFunction $function) => [
                            'type' => 'function',
                            'function' => [
                                'name' => $function->name(),
                                'description' => method_exists($function, 'description')
                                    ? $function->description()
                                    : null,
                                'parameters' => $function->schema(),
                            ],
                        ]),
                    'messages' => collect($prompt->messages())
                        ->prepend(new TextMessage(role: Role::System, content: $prompt->system()))
                        // Multimodal messages are not supported by the Groq API, so we need to extract the text content
                        ->map(fn (Message $message) => match ($message::class) {
                            MultimodalMessage::class => new TextMessage(
                                role: $message->role,
                                content: collect($message->content)
                                    ->map(fn (\Capevace\MagicImport\LLM\Message\MultimodalMessage\Text|\Capevace\MagicImport\LLM\Message\MultimodalMessage\Base64Image $content) => match ($content::class) {
                                        \Capevace\MagicImport\LLM\Message\MultimodalMessage\Text::class => $content->text,
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
                    rateLimits: \Capevace\MagicImport\LLM\Exceptions\RateLimitExceeded\RateLimits::parseRateLimitError($e->getMessage()),
                );
            }

            throw new UnknownInferenceException($e->getMessage(), previous: $e);
        }
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

    public function cost(): ?ModelCost
    {
        return ModelCost::free();
    }

    public function send(Prompt $prompt): array
    {
        // TODO: Implement send() method.
    }
}
