<?php

namespace App\Models\ApiKey;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Collection;
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
    case Google = 'google';
    case Mistral = 'mistral';

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
            self::Google => 'Google AI Studio',
            self::Mistral => 'Mistral',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            default => 'Unknown API provider',
            self::Anthropic => 'Anthropic is dedicated to advancing AI safety and reliability. Founded in 2021, this U.S.-based startup focuses on researching and deploying AI models that prioritize public safety and ethical considerations.',
            self::AwsBedrock => 'Amazon Bedrock offers a versatile, fully managed service that provides access to a variety of high-performing foundation models from leading AI companies. It simplifies the integration of advanced AI capabilities into applications through a unified API.',
            self::AzureOpenAI => 'Azure OpenAI Service delivers robust REST API access to OpenAI\'s cutting-edge language models, including GPT-4 and GPT-3.5-Turbo. It empowers developers to build intelligent applications with state-of-the-art natural language processing capabilities.',
            self::Groq => 'Groq specializes in high-performance AI processing with its Language Processing Unit (LPU). Their cloud-based platform supports the development and deployment of large-scale AI models, offering scalable and efficient solutions for natural language and data processing.',
            self::OpenAI => 'OpenAI is the pioneering force in AI, renowned for inventing groundbreaking models like ChatGPT. As the "big kid on the block," OpenAI\'s cloud-based platform provides unparalleled access to advanced AI capabilities, setting the industry standard for natural language processing and beyond.',
            self::TogetherAI => 'TogetherAI fosters collaboration in AI development by offering a scalable platform for building and deploying AI models. With a focus on community and openness, TogetherAI provides access to a wide range of pre-trained models and tools for various AI applications.',
            self::OpenRouter => 'OpenRouter simplifies the integration of AI models into applications with its scalable API. By providing access to a diverse set of pre-trained models, OpenRouter enables developers to leverage advanced AI capabilities across natural language processing, computer vision, and more.',
            self::Google => 'Google\'s AI platform is a comprehensive solution for building and deploying AI models. With a scalable API and access to a wide range of pre-trained models, Google empowers developers to create intelligent applications that leverage the latest advancements in AI technology.',
            self::Mistral => 'Mistral is the EU frontrunner in AI, showcasing great potential with its dynamic platform for AI model development and deployment. Their scalable API provides access to advanced tools for natural language processing and computer vision, positioning Mistral as a rising star in the global AI landscape.',
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
            self::Google => 'logos-google',
            self::Mistral => 'logos-mistral',
            default => 'bi-robot',
        };
    }

    public function getLink(): ?string
    {
        return match ($this) {
            self::Anthropic => 'https://anthropic.com',
            self::AwsBedrock => 'https://aws.amazon.com/bedrock',
            self::AzureOpenAI => 'https://azure.microsoft.com/en-us/products/ai-services/openai-service',
            self::Groq => 'https://groq.com',
            self::OpenAI => 'https://openai.com',
            self::TogetherAI => 'https://together.ai',
            self::OpenRouter => 'https://openrouter.ai',
            self::Google => 'https://aistudio.google.com',
            self::Mistral => 'https://mistral.ai',
            default => null,
        };
    }

    public function isConfigurable(): bool
    {
        return match ($this) {
            self::Unknown,
            self::AwsBedrock,
            self::AzureOpenAI,
            self::Groq => false,
            default => true,
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
            'google' => self::Google,
            'mistral' => self::Mistral,
            default => null,
        };
    }

    public static function default(): ?self
    {
        return self::tryFromModelString(Magic::defaultModelName());
    }

    public static function getConfigurable(): Collection
    {
        return collect(self::cases())
            ->filter(fn (self $provider) => $provider->isConfigurable())
            ->sortBy(fn (self $provider) => $provider->getLabel());
    }
}
