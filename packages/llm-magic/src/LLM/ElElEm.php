<?php

namespace Mateffy\Magic\LLM;

use Illuminate\Support\Collection;
use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\Models\BedrockClaude3Family;
use Mateffy\Magic\LLM\Models\Claude3Family;
use Mateffy\Magic\LLM\Models\GeminiFamily;
use Mateffy\Magic\LLM\Models\Gpt4Family;
use Mateffy\Magic\LLM\Models\GroqLlama3;
use Mateffy\Magic\LLM\Models\GroqMixtral8X7B;
use Mateffy\Magic\LLM\Models\OpenRouter;
use Mateffy\Magic\LLM\Models\TogetherAI;
use Mateffy\Magic\LLM\Options\ElElEmOptions;
use Illuminate\Support\Str;

/**
 * @template T of ElElEmOptions
 */
abstract class ElElEm implements LLM
{
    public function __construct(
        public readonly Organization $organization,
        public readonly string $model,

        /** @var T $options */
        public ElElEmOptions $options,
    ) {}

    public function withOptions(array $data): static
    {
        $this->options = $this->options->withOptions($data);

        return $this;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /**
     * @return T
     */
    public function getOptions(): ElElEmOptions
    {
        return $this->options;
    }

    public function getModelName(): string
    {
        return $this->model;
    }

    public function getModelCost(): ?ModelCost
    {
        return null;
    }

    public static function fromString(string $value): LLM
    {
        $organization = Str::before($value, '/');
        $model = Str::after($value, '/');

        return match ($organization) {
            'openrouter' => new OpenRouter(model: $model),
            'togetherai' => new TogetherAI(model: $model),
            'anthropic' => match ($model) {
                'claude-3-haiku' => new Claude3Family(model: Claude3Family::HAIKU),
                'claude-3-sonnet' => new Claude3Family(model: Claude3Family::SONNET),
                'claude-3-opus' => new Claude3Family(model: Claude3Family::OPUS),
                'claude-3.5-sonnet' => new Claude3Family(model: Claude3Family::SONNET_3_5),
                Claude3Family::HAIKU,
                Claude3Family::SONNET,
                Claude3Family::OPUS,
                Claude3Family::SONNET_3_5 => new Claude3Family(model: $model),
            },
            'bedrock' => match ($model) {
                'claude-3-haiku' => new BedrockClaude3Family(model: BedrockClaude3Family::HAIKU),
                'claude-3-sonnet' => new BedrockClaude3Family(model: BedrockClaude3Family::SONNET),
                'claude-3-opus' => new BedrockClaude3Family(model: BedrockClaude3Family::OPUS),
                'claude-3.5-sonnet' => new BedrockClaude3Family(model: BedrockClaude3Family::SONNET_3_5),
                BedrockClaude3Family::HAIKU,
                BedrockClaude3Family::SONNET,
                BedrockClaude3Family::SONNET_3_5,
                BedrockClaude3Family::OPUS => new BedrockClaude3Family(model: $model),
            },
            'openai' => match ($model) {
                default => new Gpt4Family(model: str($model)->after('openai/')),
            },
            'google' => new GeminiFamily(model: $model),
            'groq' => match (true) {
                Str::startsWith($model, 'llama-') => new GroqLlama3(model: $model),
                default => throw new \InvalidArgumentException("Invalid model type: {$value}"),
            },
            default => throw new \InvalidArgumentException("Invalid model type: {$value}"),
        };
    }

    public static function fromArray(array $data): ?LLM
    {
        if ($data['model'] ?? null === null) {
            throw new \InvalidArgumentException('Missing model key in model data');
        }

        return self::fromString($data['model'])
            ?->withOptions($data['options'] ?? []);
    }

    public static function id(string $organization, string $model): string
    {
        return "{$organization}/{$model}";
    }

    public static function model(string $model): static
    {
        return new static(
            model: $model,
            options: new ElElEmOptions,
        );
    }

    protected static function prefixModels(array|Collection $models, ?string $prefix = null, ?string $prefixLabels = null): Collection
    {
        return Collection::wrap($models)
            ->when($prefix, fn ($models) => $models->mapWithKeys(fn ($name, $key) => ["{$prefix}/{$key}" => $name]))
            ->when($prefixLabels, fn ($models) => $models->mapWithKeys(fn ($name, $key) => [$key => "[$prefixLabels]: {$name}"]));
    }

    public static function models(?string $prefix = 'togetherai', ?string $prefixLabels = 'TogetherAI'): Collection
    {
        return static::prefixModels([], $prefix, $prefixLabels);
    }
}
