<?php

namespace Mateffy\Magic\Prompt;

use Akaunting\Money\Money;
use Mateffy\Magic\LLM\ModelCost;
use Illuminate\Contracts\Support\Arrayable;

readonly class TokenStats implements Arrayable
{
    public function __construct(
        public int $tokens,
        public ?int $inputTokens = null,
        public ?int $outputTokens = null,

        public ?ModelCost $cost = null,
    ) {}

    public function add(TokenStats $stats): TokenStats
    {
        return new TokenStats(
            tokens: $this->tokens + $stats->tokens,
            inputTokens: ($this->inputTokens ?? 0) + ($stats->inputTokens ?? 0),
            outputTokens: ($this->outputTokens ?? 0) + ($stats->outputTokens ?? 0),
            cost: $this->cost,
        );
    }

    public function calculateInputCost(): ?Money
    {
        return $this->cost && $this->inputTokens !== null
            ? Money::EUR($this->cost->inputCostInCents($this->inputTokens))
            : null;
    }

    public function calculateOutputCost(): ?Money
    {
        return $this->cost && $this->outputTokens !== null
            ? Money::EUR($this->cost->outputCostInCents($this->tokens))
            : null;
    }

    public function calculateTotalCost(): ?Money
    {
        $input = $this->calculateInputCost();
        $output = $this->calculateOutputCost();

        if ($input !== null && $output !== null) {
            return $input->add($output);
        }

        return null;
    }

    public static function total(int $tokens, ?ModelCost $cost = null): TokenStats
    {
        return new TokenStats(tokens: $tokens, inputTokens: null, outputTokens: null, cost: $cost);
    }

    public static function withInputAndOutput(?int $inputTokens, ?int $outputTokens, ?ModelCost $cost = null): TokenStats
    {
        return new TokenStats(tokens: $inputTokens + $outputTokens, inputTokens: $inputTokens, outputTokens: $outputTokens, cost: $cost);
    }

    public function withCost(ModelCost $cost): self
    {
        return new TokenStats(
            tokens: $this->tokens,
            inputTokens: $this->inputTokens,
            outputTokens: $this->outputTokens,
            cost: $cost,
        );
    }

    public static function fromArray(array $data): self
    {
        return new TokenStats(
            tokens: $data['tokens'],
            inputTokens: $data['input_tokens'],
            outputTokens: $data['output_tokens'],
            cost: $data['cost'] ? ModelCost::fromArray($data['cost']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'tokens' => $this->tokens,
            'input_tokens' => $this->inputTokens,
            'output_tokens' => $this->outputTokens,
            'cost' => $this->cost?->toArray(),
        ];
    }
}
