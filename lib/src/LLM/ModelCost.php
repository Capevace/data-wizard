<?php

namespace Mateffy\Magic\LLM;

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
        return Money::EUR($this->inputCentsPer1K)->multiply(1000);
    }

    public function outputPricePer1M(): Money
    {
        return Money::EUR($this->outputCentsPer1K)->multiply(1000);
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

    public function formatAveragePer1M(): string
    {
        $average = $this->inputPricePer1M()->add($this->outputPricePer1M())->divide(2);

        return __(':value / 1M Tokens', ['value' => $average->format()]);
    }

    public static function free(): ModelCost
    {
        return new ModelCost(0, 0);
    }

    /**
     * Helper function to convert a price per million tokens (e.g. 3 $/million) to cents per thousand tokens (e.g. 0.03 $/thousand)
     */
    public static function pricePerMillionToCentsPerThousands(float $pricePerMillionTokens): float
    {
        //    // e.g. 3 $/million => 0,000003 $/token
        //    $pricePerToken = $pricePerMillionTokens / 1_000_000;
        //
        //    // e.g. 0,000003 $/token => 0,003 cents/token
        //    $centsPerToken = $pricePerToken * 100;
        //
        //    // e.g. 0,0003 cents/token => 0,3 cents/thousand tokens
        //    $centsPerThousandTokens = $centsPerToken * 1000;

        //    3 => 0,3 results in divisor of 10 => cents per thousand tokens = price per million tokens / 10

        // As we could infer from the result, we can just perform a simple multiplication of dividing by 10.
        // But: this kind of compute is cheap, and having the local steps written out makes it easier to understand,
        // should this ever need to be changed.

        return $pricePerMillionTokens / 10;
    }
}
