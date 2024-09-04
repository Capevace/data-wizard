<?php

namespace Capevace\MagicImport\LLM\Models;

use Capevace\MagicImport\Config\Organization;
use Capevace\MagicImport\LLM\ElElEm;
use Capevace\MagicImport\LLM\Models\Concerns\UsesAnthropicApi;
use Capevace\MagicImport\LLM\Options\ElElEmOptions;
use Capevace\MagicImport\Model\ModelCost;

class Claude3Family extends ElElEm
{
    use UsesAnthropicApi;

    public const HAIKU = 'claude-3-haiku-20240307';
    public const SONNET = 'claude-3-sonnet-20240229';
    public const OPUS = 'claude-3-opus-20240229';
    public const SONNET_3_5 = 'claude-3-5-sonnet-20240620';

    public function __construct(
        string $model,
        ElElEmOptions $options = new ElElEmOptions,
    )
    {
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
                inputCentsPer1K: 0.025,
                outputCentsPer1K: 0.125
            ),
            Claude3Family::SONNET,
            Claude3Family::SONNET_3_5, => new ModelCost(
                inputCentsPer1K: 0.3,
                outputCentsPer1K: 1.5
            ),
            default => null,
        };
    }
}
