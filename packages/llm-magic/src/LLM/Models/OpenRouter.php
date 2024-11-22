<?php

namespace Mateffy\Magic\LLM\Models;

use Illuminate\Support\Collection;
use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\Models\Apis\UsesGroqApi;
use Mateffy\Magic\LLM\Models\Apis\UsesOpenAiApi;
use Mateffy\Magic\LLM\Options\ElElEmOptions;

class OpenRouter extends ElElEm
{
    use UsesOpenAiApi;

    public const X_AI_GROK_BETA = 'x-ai/grok-beta';

    public const GOOGLE_GEMINI_1_5_PRO = 'google/gemini-pro-1.5';
    public const GOOGLE_GEMINI_1_5_FLASH_8B = 'google/gemini-flash-1.5-8b';
    public const GOOGLE_GEMINI_1_5_FLASH = 'google/gemini-flash-1.5';

    public const MISTRAL_7B_INSTRUCT = 'mistralai/mistral-7b-instruct';
    public const MISTRAL_LARGE = 'mistralai/mistral-large';
    public const MISTRAL_NEMO = 'mistralai/mistral-nemo';
    public const MISTRAL_TINY = 'mistralai/mistral-tiny';

    public const COHERE_COMMAND_R = 'cohere/command-r-08-2024';


    public const META_LLAMA_3_1_8B_INSTRUCT = 'meta-llama/llama-3.1-8b-instruct';
    public const META_LLAMA_3_1_70B_INSTRUCT = 'meta-llama/llama-3.1-70b-instruct';
    public const META_LLAMA_3_1_405B_INSTRUCT = 'meta-llama/llama-3.1-405b-instruct';
    public const META_LLAMA_3_1_405B_INSTRUCT_FREE = 'meta-llama/llama-3.1-405b-instruct:free';
    public const META_LLAMA_3_2_1B_INSTRUCT = 'meta-llama/llama-3.2-1b-instruct';
    public const META_LLAMA_3_2_3B_INSTRUCT = 'meta-llama/llama-3.2-3b-instruct';
    public const META_LLAMA_3_2_11B_VISION_INSTRUCT = 'meta-llama/llama-3.2-11b-vision-instruct';
    public const META_LLAMA_3_2_90B_VISION_INSTRUCT = 'meta-llama/llama-3.2-90b-vision-instruct';

    public const MICROSOFT_WIZARDLM_2_8X22B = 'microsoft/wizardlm-2-8x22b';

    public const NOUS_HERMES_3_405B_INSTRUCT_FREE = 'nousresearch/hermes-3-llama-3.1-405b:free';
    public const NOUS_HERMES_3_405B_INSTRUCT = 'nousresearch/hermes-3-llama-3.1-405b';

    public const PERPLEXITY_LLAMA_3_1_SONAR_70B = 'perplexity/llama-3.1-sonar-large-128k-chat';
    public const PERPLEXITY_LLAMA_3_1_SONAR_70B_ONLINE = 'perplexity/llama-3.1-sonar-large-128k-online';

    public static function models(?string $prefix = 'openrouter', ?string $prefixLabels = 'OpenRouter'): Collection
    {
        return static::prefixModels([
            static::COHERE_COMMAND_R => 'Cohere Command R',
            static::X_AI_GROK_BETA => 'xAI Grok Beta',
            static::GOOGLE_GEMINI_1_5_PRO => 'Google Gemini Pro 1.5',
            static::GOOGLE_GEMINI_1_5_FLASH_8B => 'Google Gemini Flash 1.5 8B',
            static::GOOGLE_GEMINI_1_5_FLASH => 'Google Gemini Flash 1.5',
            static::MISTRAL_7B_INSTRUCT => 'Mistral 7B Instruct',
            static::MISTRAL_LARGE => 'Mistral Large',
            static::MISTRAL_NEMO => 'Mistral Nemo',
            static::MISTRAL_TINY => 'Mistral Tiny',
            static::META_LLAMA_3_1_8B_INSTRUCT => 'Meta Llama 3.1 8B Instruct',
            static::META_LLAMA_3_2_1B_INSTRUCT => 'Meta Llama 3.2 1B Instruct',
            static::MICROSOFT_WIZARDLM_2_8X22B => 'Microsoft WizardLM 2 8x22B',
            static::NOUS_HERMES_3_405B_INSTRUCT_FREE => 'Nous Hermes 3 405B Instruct Free',
            static::NOUS_HERMES_3_405B_INSTRUCT => 'Nous Hermes 3 405B Instruct',
            static::PERPLEXITY_LLAMA_3_1_SONAR_70B => 'Perplexity Llama 3.1 Sonar 70B',
            static::PERPLEXITY_LLAMA_3_1_SONAR_70B_ONLINE => 'Perplexity Llama 3.1 Sonar 70B Online',
        ], $prefix, $prefixLabels);
    }

    protected function getOpenAiApiKey(): string
    {
        return config('magic-extract.apis.openrouter.token');
    }

    protected function getOpenAiBaseUri(): ?string
    {
        return config('magic-extract.apis.openrouter.url');
    }

    public function __construct(
        string $model,
        public ElElEmOptions $options = new ElElEmOptions,
    ) {
        parent::__construct(
            organization: new Organization(
                id: 'openrouter',
                name: 'OpenRouter',
                website: 'https://openrouter.ai',
                privacyUsedForModelTraining: true,
                privacyUsedForAbusePrevention: true,
            ),
            model: $model,
            options: $options,
        );
    }

    public static function model(string $model): static
    {
        return new static(
            model: $model,
            options: new ElElEmOptions,
        );
    }

    public static function grok(): static
    {
        return new static(
            model: self::X_AI_GROK_BETA,
            options: new ElElEmOptions,
        );
    }
}
