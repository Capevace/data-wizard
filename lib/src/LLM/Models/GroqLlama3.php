<?php

namespace Mateffy\Magic\LLM\Models;

use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\Models\Apis\UsesGroqApi;
use Mateffy\Magic\LLM\Options\ElElEmOptions;

class GroqLlama3 extends Llama3Family
{
    use UsesGroqApi;

    public const LLAMA_3_70B = 'llama3-70b-8192';

    public const MIXTRAL_8X7B = 'mixtral-8x7b';

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
}
