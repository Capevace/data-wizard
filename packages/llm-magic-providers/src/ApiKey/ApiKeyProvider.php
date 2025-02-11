<?php

namespace Mateffy\Magic\Providers\ApiKey;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;
use Mateffy\Magic;

enum ApiKeyProvider: string implements HasIcon, HasLabel
{
    case Unknown = 'unknown';
    case Anthropic = 'anthropic';
    case AwsBedrock = 'aws-bedrock';
    case AzureOpenAI = 'azure-openai';
    case Groq = 'groq';
    case OpenAI = 'openai';
    case TogetherAI = 'togetherai';
    case OpenRouter = 'openrouter';

    public function getLabel(): string
    {
        return match ($this) {
            default => 'Unknown',
            self::Anthropic => 'Anthropic',
            self::AwsBedrock => 'AWS Bedrock',
            self::AzureOpenAI => 'Azure OpenAI',
            self::Groq => 'Groq',
            self::OpenAI => 'OpenAI',
            self::TogetherAI => 'TogetherAI',
            self::OpenRouter => 'OpenRouter',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            default => 'Unknown API provider',
            self::Anthropic => 'Anthropic PBC is a U.S.-based artificial intelligence startup public-benefit company, founded in 2021. It researches and develops AI to "study their safety properties at the technological frontier" and use this research to deploy safe, reliable models for the public.',
            self::AwsBedrock => 'Amazon Bedrock is a fully managed service that offers a choice of high-performing foundation models (FMs) from leading AI companies like AI21 Labs, Anthropic, Cohere, Meta, Mistral AI, Stability AI, and Amazon through a single API.',
            self::AzureOpenAI => 'Azure OpenAI Service provides REST API access to OpenAI\'s powerful language models including GPT-4o, GPT-4 Turbo with Vision, GPT-4, GPT-3.5-Turbo, and Embeddings model series.',
            self::Groq => 'Groq provides a cloud-based platform for building and deploying AI models, featuring a scalable API. The Language Processing Unit (LPU) is designed for high-performance processing of natural language and other data types. The platform supports the development and deployment of large-scale AI models.',
            self::OpenAI => 'OpenAI offers a cloud-based platform for building and deploying AI models, featuring a scalable API. The platform provides access to pre-trained models and tools for natural language processing, computer vision, and more. With the invention of ChatGPT, OpenAI has become a leading provider of AI-powered language models.',
            self::TogetherAI => 'TogetherAI is a cloud-based platform for building and deploying AI models, featuring a scalable API. The platform provides access to pre-trained models and tools for natural language processing, computer vision, and more. TogetherAI is a leading provider of AI-powered language models.',
            self::OpenRouter => 'OpenRouter is a cloud-based platform for building and deploying AI models, featuring a scalable API. The platform provides access to pre-trained models and tools for natural language processing, computer vision, and more. OpenRouter is a leading provider of AI-powered language models.',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Anthropic => 'logos-anthropic',
            self::AwsBedrock => 'logos-aws',
            self::AzureOpenAI => 'logos-microsoft',
            self::OpenAI => 'logos-openai',
            self::OpenRouter => 'logos-openrouter',
            default => 'bi-robot',
        };
    }

    /**
     * @return ApiKeyTokenType[]
     */
    public function getValidTypes(): array
    {
        return match ($this) {
            self::OpenAI => [ApiKeyTokenType::Token, ApiKeyTokenType::Organization],
            default => [ApiKeyTokenType::Token],
        };
    }

    public static function tryFromModelString(?string $model): ?self
    {
        if ($model === null) {
            return null;
        }

        $str = str($model);

        return match ($str->before('/')->toString()) {
            'anthropic' => self::Anthropic,
            'aws-bedrock' => self::AwsBedrock,
            'azure-openai' => self::AzureOpenAI,
            'groq' => self::Groq,
            'openai' => self::OpenAI,
            'togetherai' => self::TogetherAI,
            'openrouter' => self::OpenRouter,
            default => null,
        };
    }

    public static function default(): ?self
    {
        return self::tryFromModelString(Magic::defaultModelName());
    }
}
