<?php

namespace Capevace\MagicImport\LLM\Models;

use Capevace\MagicImport\Config\Organization;
use Capevace\MagicImport\LLM\ElElEm;
use Capevace\MagicImport\LLM\Options\ChatGptOptions;

readonly class Gpt4Family extends ElElEm
{
    public function __construct(
        string $model,
        ChatGptOptions $options = new ChatGptOptions,
    )
    {
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
}