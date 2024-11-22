<?php

namespace Mateffy\Magic\LLM\Models;

use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\MessageCollection;
use Mateffy\Magic\LLM\Models\Apis\UsesOpenAiApi;
use Mateffy\Magic\LLM\Options\ChatGptOptions;
use Mateffy\Magic\Prompt\Prompt;

class GeminiFamily extends ElElEm
{
    use UsesOpenAiApi;

    public function __construct(
        string $model,
        ChatGptOptions $options = new ChatGptOptions,
    ) {
        parent::__construct(
            organization: new Organization(
                id: 'google',
                name: 'Google',
                website: 'https://google.com',
                privacyUsedForModelTraining: true,
                privacyUsedForAbusePrevention: true,
            ),
            model: $model,
            options: $options,
        );
    }

    protected function getOpenAiApiKey(): string
    {
        return config('magic-extract.apis.gemini.token');
    }

    protected function getOpenAiOrganization(): ?string
    {
        return null;
    }

    protected function getOpenAiBaseUri(): ?string
    {
        return 'generativelanguage.googleapis.com/v1beta/openai';
    }

    public function send(Prompt $prompt): MessageCollection
    {
        // TODO: Implement send() method.
    }

    public static function flash(): static
    {
        return new static(
            model: 'gemini-1.5-flash',
            options: new ChatGptOptions,
        );
    }
}
