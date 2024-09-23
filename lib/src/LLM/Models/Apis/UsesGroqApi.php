<?php

namespace Mateffy\Magic\LLM\Models\Apis;

use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Exceptions\RateLimitExceeded;
use Mateffy\Magic\LLM\Exceptions\UnknownInferenceException;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\LLM\Message\TextMessage;
use Mateffy\Magic\LLM\ModelCost;
use Mateffy\Magic\LLM\Models\Decoders\ResponseDecoder;
use Mateffy\Magic\Prompt\Prompt;
use Mateffy\Magic\Prompt\Role;
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
                                    ->map(fn (\Mateffy\Magic\LLM\Message\MultimodalMessage\Text|\Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image $content) => match ($content::class) {
                                        \Mateffy\Magic\LLM\Message\MultimodalMessage\Text::class => $content->text,
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
                    rateLimits: \Mateffy\Magic\LLM\Exceptions\RateLimitExceeded\RateLimits::parseRateLimitError($e->getMessage()),
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
