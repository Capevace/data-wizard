<?php

namespace Capevace\MagicImport\Model\Anthropic;

use Capevace\MagicImport\Model\ModelCost;

readonly class Claude3Haiku extends Claude3
{
    public function name(): string
    {
        return 'claude-3-haiku-20240307';
    }

    public function cost(): ?ModelCost
    {
        /**
         * Haiku
         * Input: $0.25 / MTok
         * Output: $1.25 / MTok
         */
        return new ModelCost(
            inputCentsPer1K: 0.025,
            outputCentsPer1K: 0.125
        );
    }
}
