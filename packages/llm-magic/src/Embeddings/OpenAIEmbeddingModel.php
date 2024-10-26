<?php

namespace Mateffy\Magic\Embeddings;

use Mateffy\Magic\Config\Organization;
use Mateffy\Magic\Embeddings\Providers\UsesOpenAIEmbeddingsApi;
use Mateffy\Magic\LLM\ModelCost;

readonly class OpenAIEmbeddingModel extends EmbeddingModel
{
    use UsesOpenAIEmbeddingsApi;

    public const TEXT_EMBEDDING_3_SMALL = 'text-embedding-3-small';
    public const TEXT_EMBEDDING_3_LARGE = 'text-embedding-3-large';
    public const TEXT_EMBEDDING_ADA_002 = 'text-embedding-ada-002';
    public function __construct(
        string $model,
        ?ModelCost $cost = null
    )
    {
        parent::__construct(
            organization: new Organization(
                id: 'openai',
                name: 'OpenAI',
                website: 'https://openai.com',
                privacyUsedForModelTraining: true,
                privacyUsedForAbusePrevention: true,
            ),
            model: $model,
            cost: $cost
        );
    }


    public static function text_3_small(): OpenAIEmbeddingModel
    {
        return new OpenAIEmbeddingModel(
            model: 'text-embedding-3-small',
            cost: new ModelCost(
                inputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(0.02),
                outputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(0.01)
            )
        );
    }

    public static function text_3_large(): OpenAIEmbeddingModel
    {
        return new OpenAIEmbeddingModel(
            model: 'text-embedding-3-large',
            cost: new ModelCost(
                inputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(0.13),
                outputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(0.065)
            )
        );
    }

    public static function text_ada_002(): OpenAIEmbeddingModel
    {
        return new OpenAIEmbeddingModel(
            model: 'text-embedding-ada-002',
            cost: new ModelCost(
                inputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(0.1),
                outputCentsPer1K: ModelCost::pricePerMillionToCentsPerThousands(0.05)
            )
        );
    }
}

