<?php

namespace Mateffy\Magic\LLM\Models;

use Illuminate\Support\Collection;
use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\ElElEm;
use Mateffy\Magic\LLM\ModelCost;
use Mateffy\Magic\LLM\Models\Apis\UsesAnthropicApi;
use Mateffy\Magic\LLM\Options\ElElEmOptions;

class Claude3Family extends ElElEm
{
    use UsesAnthropicApi;

    public const HAIKU = 'claude-3-haiku-20240307';

    public const SONNET = 'claude-3-sonnet-20240229';

    public const OPUS = 'claude-3-opus-20240229';

    public const SONNET_3_5 = 'claude-3-5-sonnet-20240620';
    public const SONNET_3_5_COMPUTER_USE = 'claude-3-5-sonnet-20241022';

    public function __construct(
        string $model,
        ElElEmOptions $options = new ElElEmOptions,
    ) {
        parent::__construct(
            organization: new Organization(
                id: 'anthropic',
                name: 'Anthropic',
                website: 'https://anthropic.com',
                privacyUsedForModelTraining: true,
                privacyUsedForAbusePrevention: true,
            ),
            model: $model,
            options: $options,
        );
    }

    public function getModelCost(): ?ModelCost
    {
        return match ($this->model) {
            Claude3Family::HAIKU => new ModelCost(
                inputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(0.25),
                outputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(1.25)
            ),
            Claude3Family::SONNET,
            Claude3Family::SONNET_3_5, => new ModelCost(
                inputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(3),
                outputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(15)
            ),
            Claude3Family::OPUS => new ModelCost(
                inputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(15),
                outputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(75)
            ),
            default => null,
        };
    }

    public static function haiku(): Claude3Family
    {
        return new Claude3Family(
            model: Claude3Family::HAIKU,
            options: new ElElEmOptions
        );
    }

    public static function sonnet(): Claude3Family
    {
        return new Claude3Family(
            model: Claude3Family::SONNET,
            options: new ElElEmOptions
        );
    }

    public static function sonnet_3_5(): Claude3Family
    {
        return new Claude3Family(
            model: Claude3Family::SONNET_3_5,
            options: new ElElEmOptions
        );
    }

    public static function sonnet_3_5_computer_use(): Claude3Family
    {
        return new Claude3Family(
            model: Claude3Family::SONNET_3_5_COMPUTER_USE,
            options: new ElElEmOptions
        );
    }

    public static function opus(): Claude3Family
    {
        return new Claude3Family(
            model: Claude3Family::OPUS,
            options: new ElElEmOptions
        );
    }

    public static function models(?string $prefix = null, ?string $prefixLabels = 'Claude API'): Collection
    {
        return static::prefixModels([
            static::HAIKU => 'Claude 3 Haiku',
            static::SONNET => 'Claude 3 Sonnet',
            static::OPUS => 'Claude 3 Opus',
            static::SONNET_3_5 => 'Claude 3.5 Sonnet',
        ], $prefix, $prefixLabels);
    }
}
