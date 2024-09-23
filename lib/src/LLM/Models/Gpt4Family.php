<?php

namespace Mateffy\Magic\LLM\Models;

use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\Options\ChatGptOptions;

abstract class Gpt4Family extends ElElEm
{
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
}
