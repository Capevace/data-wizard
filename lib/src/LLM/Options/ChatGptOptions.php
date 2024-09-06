<?php

namespace Capevace\MagicImport\LLM\Options;

readonly class ChatGptOptions extends ElElEmOptions
{
    public function __construct(
        public int $maxTokens = ElElEmOptions::DEFAULT_MAX_TOKENS,
        public float $temperature = 0,
        public float $topP = 1,
    ) {
        parent::__construct(maxTokens: $maxTokens);
    }

    public function withOptions(array $data): static
    {
        return new static(
            maxTokens: $data['maxTokens'] ?? $this->maxTokens,
            temperature: $data['temperature'] ?? $this->temperature,
            topP: $data['topP'] ?? $this->topP,
        );
    }
}
