<?php

namespace Mateffy\Magic\LLM\Models;

use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\Options\ElElEmOptions;

class GroqMixtral8X7B extends Mixtral8x7b
{
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
