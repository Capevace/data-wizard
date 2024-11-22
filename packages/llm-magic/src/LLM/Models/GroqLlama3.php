<?php

namespace Mateffy\Magic\LLM\Models;

use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\Models\Apis\UsesGroqApi;
use Mateffy\Magic\LLM\Models\Apis\UsesOpenAiApi;
use Mateffy\Magic\LLM\Options\ElElEmOptions;

class GroqLlama3 extends Llama3Family
{
    use UsesOpenAiApi;

    public const LLAMA_3_70B = 'llama3-70b-8192';

    public const MIXTRAL_8X7B = 'mixtral-8x7b';

    public const LLAMA_3_2_3B = 'llama-3.2-3b-preview';

    protected function getOpenAiApiKey(): string
    {
        return config('magic-extract.apis.groq.token');
    }

    protected function getOpenAiBaseUri(): ?string
    {
        return 'api.groq.com/openai/v1';
    }

    public function __construct(
        string $model,
        public ElElEmOptions $options = new ElElEmOptions,
    ) {
        parent::__construct(
            organization: new Organization(
                id: 'groq',
                name: 'Groq',
                website: 'https://groq.com',
                privacyUsedForModelTraining: false,
                privacyUsedForAbusePrevention: false,
            ),
            model: $model,
            options: $options,
        );
    }

    public static function llama_3_2_3b(): static
    {
        return new static(
            model: self::LLAMA_3_2_3B,
            options: new ElElEmOptions,
        );
    }
}
