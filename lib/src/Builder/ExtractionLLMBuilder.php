<?php

namespace Mateffy\Magic\Builder;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mateffy\Magic\Builder\Concerns\HasArtifacts;
use Mateffy\Magic\Builder\Concerns\HasExtractionModelCallbacks;
use Mateffy\Magic\Builder\Concerns\HasModel;
use Mateffy\Magic\Builder\Concerns\HasModelCallbacks;
use Mateffy\Magic\Builder\Concerns\HasSchema;
use Mateffy\Magic\Builder\Concerns\HasStrategy;
use Mateffy\Magic\Builder\Concerns\HasSystemPrompt;
use Mateffy\Magic\Builder\Concerns\HasTools;
use Mateffy\Magic\Config\Extractor;
use Mateffy\Magic\Config\ExtractorFileType;
use Mateffy\Magic\Strategies\Strategy;

class ExtractionLLMBuilder
{
    use HasArtifacts;
    use HasExtractionModelCallbacks;
    use HasModel;
    use HasModelCallbacks;
    use HasSchema;
    use HasStrategy;
    use HasSystemPrompt;
    use HasTools;

    public function stream(): Collection
    {
        $strategyClass = $this->getStrategyClass();

        /** @var Strategy $strategy */
        $strategy = new $strategyClass(
            extractor: new Extractor(
                id: Str::uuid()->toString(),
                title: 'Title',
                outputInstructions: 'Output instructions',
                allowedTypes: [ExtractorFileType::IMAGES, ExtractorFileType::DOCUMENTS],
                llm: $this->model,
                schema: $this->schema,
                strategy: 'simple'
            ),
            onMessageProgress: $this->onMessageProgress,
            onMessage: $this->onMessage,
            onTokenStats: $this->onTokenStats,
            onActorTelemetry: $this->onActorTelemetry,
            onDataProgress: $this->onDataProgress,
        );

        $result = $strategy->run($this->artifacts);

        return collect($result->value);
    }

    public function send(): Collection
    {
        return collect();
    }
}
