<?php

namespace Capevace\MagicImport\Model\Anthropic;

use Capevace\MagicImport\Model\ModelCost;

readonly class Claude3Sonnet extends Claude3
{
    public function name(): string
    {
        return 'claude-3-sonnet-20240229';
    }

    public function cost(): ?ModelCost
    {
        /**
         * Sonnet
         * Input: $3 / MTok
         * Output: $15 / MTok
         */

        return new ModelCost(
            inputCentsPer1K: 0.3,
            outputCentsPer1K: 1.5
        );
    }
}
