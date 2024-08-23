<?php

namespace Capevace\MagicImport\LLM\Models;

use Capevace\MagicImport\Config\Organization;
use Capevace\MagicImport\LLM\Options\ElElEmOptions;

readonly class GroqLlama3 extends Llama3Family
{
    public function __construct(
        string $model,
        public ElElEmOptions $options = new ElElEmOptions,
    )
    {
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