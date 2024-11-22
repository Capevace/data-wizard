<?php

namespace Mateffy\Magic\LLM\Models;

use Illuminate\Support\Collection;
use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\Models\Apis\UsesGroqApi;
use Mateffy\Magic\LLM\Models\Apis\UsesOpenAiApi;
use Mateffy\Magic\LLM\Options\ElElEmOptions;

class TogetherAI extends ElElEm
{
    use UsesOpenAiApi;

    public const META_LLAMA_3_2_3B_VISION_INSTRUCT_TURBO = 'meta-llama/Llama-3.2-3B-Vision-Instruct-Turbo';
    public const META_LLAMA_3_2_11B_VISION_INSTRUCT_TURBO = 'meta-llama/llama-3.2-11b-vision-instruct-turbo';
    public const META_LLAMA_3_2_90B_VISION_INSTRUCT_TURBO = 'meta-llama/Llama-3.2-90B-Vision-Instruct-Turbo';

    public const META_LLAMA_3_1_8B_INSTRUCT_TURBO = 'meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo';

    // Qwen/Qwen2.5-7B-Instruct-Turbo
    public const OWEN_QWEN2_5_7B_INSTRUCT_TURBO = 'Qwen/Qwen2.5-7B-Instruct-Turbo';

    public static function models(?string $prefix = 'togetherai', ?string $prefixLabels = 'TogetherAI'): Collection
    {
        return static::prefixModels([
            static::META_LLAMA_3_1_8B_INSTRUCT_TURBO => 'Meta Llama 3.1 8B Instruct Turbo',
            static::META_LLAMA_3_2_3B_VISION_INSTRUCT_TURBO => 'Meta Llama 3.2 3B Vision Instruct Turbo',
            static::META_LLAMA_3_2_11B_VISION_INSTRUCT_TURBO => 'Meta Llama 3.2 11B Vision Instruct Turbo',
            static::META_LLAMA_3_2_90B_VISION_INSTRUCT_TURBO => 'Meta Llama 3.2 90B Vision Instruct Turbo',
            static::OWEN_QWEN2_5_7B_INSTRUCT_TURBO => 'Qwen Qwen2.5 7B Instruct Turbo',
            'mistralai/Mixtral-8x7B-Instruct-v0.1' => 'Mistral Mixtral 8x7B Instruct v0.1',
        ], $prefix, $prefixLabels);
    }

    protected function getOpenAiApiKey(): string
    {
        return config('magic-extract.apis.togetherai.token');
    }

    protected function getOpenAiBaseUri(): ?string
    {
        return config('magic-extract.apis.togetherai.url');
    }

    public function __construct(
        string $model,
        public ElElEmOptions $options = new ElElEmOptions,
    ) {
        parent::__construct(
            organization: new Organization(
                id: 'togetherai',
                name: 'TogetherAI',
                website: 'https://together.ai',
                privacyUsedForModelTraining: true,
                privacyUsedForAbusePrevention: true,
            ),
            model: $model,
            options: $options,
        );
    }
}
