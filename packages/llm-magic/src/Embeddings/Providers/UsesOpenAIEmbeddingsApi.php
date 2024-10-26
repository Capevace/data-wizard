<?php

namespace Mateffy\Magic\Embeddings\Providers;

use Closure;
use Mateffy\Magic\Embeddings\EmbeddedData;
use Mateffy\Magic\Prompt\TokenStats;
use OpenAI\Laravel\Facades\OpenAI;

trait UsesOpenAIEmbeddingsApi
{
    public function get(string $input, ?Closure $onTokenStats = null): EmbeddedData
    {
        $response = OpenAI::embeddings()
            ->create([
                'model' => $this->model,
                'input' => $input,
            ]);

        if ($response->usage && $onTokenStats) {
            $onTokenStats(new TokenStats(
                tokens: $response->usage->totalTokens,
                inputTokens: $response->usage->promptTokens,
                outputTokens: 0
            ));
        }

        return new EmbeddedData(vectors: $response->embeddings[0]->embedding);
    }
}
