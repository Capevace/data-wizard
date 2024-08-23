<?php

namespace Capevace\MagicImport\Model\Mistral;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Model\ChatLLM;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Prompt;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\StreamResponse;

readonly class Mistral8x7b implements ChatLLM
{
    public function __construct(
        public int $maxTokens = 10000,
        public float $temperature = 0.83,
        public ?string $stop = null,
        public float $frequencyPenalty = 0.11,
        public float $presencePenalty = 0.03,
    )
    {
    }

    public function name(): string
    {
        return 'gpt-4-turbo-preview';
    }

    public function parameters(): array
    {
        return [
            'model' => $this->name(),
            'max_tokens' => $this->maxTokens,
            'stop' => $this->stop,
            'frequency_penalty' => $this->frequencyPenalty,
            'presence_penalty' => $this->presencePenalty,
        ];
    }

    public function createStreamedHttpRequest(array $messages, array $functions): ?StreamResponse
    {
        return OpenAI::chat()
            ->createStreamed([
                ...$this->parameters(),
                'messages' => array_map(fn(Message $message) => $message->toArray(), $messages),
                'functions' => array_map(fn(InvokableFunction $function) => $function->schema(), $functions),
            ]);
    }
}