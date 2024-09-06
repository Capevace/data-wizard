<?php

namespace Capevace\MagicImport\LLM;

use Capevace\MagicImport\Config\Organization;
use Capevace\MagicImport\LLM\Models\BedrockClaude3Family;
use Capevace\MagicImport\LLM\Models\Claude3Family;
use Capevace\MagicImport\LLM\Models\Gpt4Family;
use Capevace\MagicImport\LLM\Models\GroqLlama3;
use Capevace\MagicImport\LLM\Models\GroqMixtral8X7B;
use Capevace\MagicImport\LLM\Options\ElElEmOptions;
use Capevace\MagicImport\Model\ModelCost;
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
            'anthropic' => match ($model) {
                Claude3Family::HAIKU,
                Claude3Family::SONNET,
                Claude3Family::OPUS,
                Claude3Family::SONNET_3_5 => new Claude3Family(model: $model),
            },
            'bedrock' => match ($model) {
                BedrockClaude3Family::HAIKU,
                BedrockClaude3Family::SONNET,
                BedrockClaude3Family::SONNET_3_5,
                BedrockClaude3Family::OPUS => new BedrockClaude3Family(model: $model),
            },
            'openai' => match ($model) {
                'gpt-4o',
                'gpt-4-turbo',
                'gpt-4' => new Gpt4Family(model: $model),
                default => throw new \InvalidArgumentException("Invalid model type: {$value}"),
            },
            'groq' => match ($model) {

                'llama-3-70b' => new GroqLlama3(model: 'llama-3-70b-8192'),
                'mixtral-8x7b' => new GroqMixtral8X7B(model: $model),
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
}
