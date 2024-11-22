<?php

namespace Mateffy\Magic\LLM\Models;

use Illuminate\Support\Collection;
use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\MessageCollection;
use Mateffy\Magic\LLM\Models\Apis\UsesOpenAiApi;
use Mateffy\Magic\LLM\Options\ChatGptOptions;
use Mateffy\Magic\Prompt\Prompt;

class Gpt4Family extends ElElEm
{
    use UsesOpenAiApi;

    public function __construct(
        string $model,
        ChatGptOptions $options = new ChatGptOptions,
    ) {
        parent::__construct(
            organization: new Organization(
                id: 'openai',
                name: 'OpenAI',
                website: 'https://openai.com',
                privacyUsedForModelTraining: true,
                privacyUsedForAbusePrevention: true,
            ),
            model: $model,
            options: $options,
        );
    }

    public static function models(?string $prefix = 'openai', ?string $prefixLabels = 'OpenAI API'): Collection
    {
        return static::prefixModels([
            'o1-preview-2024-09-12' => 'GPT-o1 Preview',
            'gpt-o1-mini' => 'GPT-o1 Mini',
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'gpt-4o' => 'GPT-4o',
            'gpt-4o-mini' => 'GPT-4o Mini',
        ], $prefix, $prefixLabels);
    }
}
