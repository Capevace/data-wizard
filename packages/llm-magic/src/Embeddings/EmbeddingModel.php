<?php

namespace Mateffy\Magic\Embeddings;


use Illuminate\Support\Str;
use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\LLM\ModelCost;

abstract readonly class EmbeddingModel
{
    public function __construct(
        public Organization $organization,
        public string $model,
        public ?ModelCost $cost = null,
    ) {}

    public static function fromString(string $value): EmbeddingModel
    {
        $organization = Str::before($value, '/');
        $model = Str::after($value, '/');

        return match ($organization) {
            'openai' => match ($model) {
                OpenAIEmbeddingModel::TEXT_EMBEDDING_ADA_002 => OpenAIEmbeddingModel::text_ada_002(),
                OpenAIEmbeddingModel::TEXT_EMBEDDING_3_LARGE => OpenAIEmbeddingModel::text_3_large(),
                OpenAIEmbeddingModel::TEXT_EMBEDDING_3_SMALL => OpenAIEmbeddingModel::text_3_small(),
                default => new OpenAIEmbeddingModel(model: $model),
            },
            default => throw new \InvalidArgumentException("Unsupported organization: {$value}"),
        };
    }

    abstract public function get(string $input): EmbeddedData;
}

