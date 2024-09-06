<?php

namespace Capevace\MagicImport\Model;

use Akaunting\Money\Money;
use Illuminate\Contracts\Support\Arrayable;

readonly class ModelCost implements Arrayable
{
    public function __construct(
        public float $inputCentsPer1K,
        public float $outputCentsPer1K,
    ) {}

    /**
     * @return float The cost in cents for the given number of tokens
     */
    public function inputCostInCents(int $tokens): float
    {
        return $this->inputCentsPer1K * ($tokens / 1000);
    }

    /**
     * @return float The cost in cents for the given number of tokens
     */
    public function outputCostInCents(int $tokens): float
    {
        return $this->outputCentsPer1K * ($tokens / 1000);
    }

    public function inputPricePer1M(): Money
    {
        return Money::EUR($this->inputCentsPer1K * 1000);
    }

    public function outputPricePer1M(): Money
    {
        return Money::EUR($this->outputCentsPer1K * 1000);
    }

    public static function fromArray(array $data): ModelCost
    {
        return new ModelCost(
            inputCentsPer1K: $data['input_cents_per_1k'],
            outputCentsPer1K: $data['output_cents_per_1k'],
        );
    }

    public function toArray(): array
    {
        return [
            'input_cents_per_1k' => $this->inputCentsPer1K,
            'output_cents_per_1k' => $this->outputCentsPer1K,
        ];
    }

    public static function free(): ModelCost
    {
        return new ModelCost(0, 0);
    }
}
