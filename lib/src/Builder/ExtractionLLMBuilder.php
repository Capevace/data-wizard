<?php

namespace Mateffy\Magic\Builder;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Mateffy\Magic\Builder\Concerns\HasArtifacts;
use Mateffy\Magic\Builder\Concerns\HasModel;
use Mateffy\Magic\Builder\Concerns\HasModelCallbacks;
use Mateffy\Magic\Builder\Concerns\HasSchema;
use Mateffy\Magic\Builder\Concerns\HasStrategy;
use Mateffy\Magic\Builder\Concerns\HasSystemPrompt;
use Mateffy\Magic\Builder\Concerns\HasTools;
use Mateffy\Magic\Builder\Concerns\LaunchesBuilderLLM;
use Mateffy\Magic\Config\Extractor;
use Mateffy\Magic\Config\ExtractorFileType;
use Mateffy\Magic\LLM\PreconfiguredModelLaunchInterface;
use Mateffy\Magic\Strategies\Strategy;

class ExtractionLLMBuilder implements PreconfiguredModelLaunchInterface
{
    use HasArtifacts;
    use HasModel;
    use HasModelCallbacks;
    use HasSchema;
    use HasStrategy;
    use HasSystemPrompt;
    use HasTools;
    use LaunchesBuilderLLM;

    public function stream(
        ?Closure $onMessageProgress = null,
        ?Closure $onMessage = null,
        ?Closure $onTokenStats = null,
        ?Closure $onActorTelemetry = null,
        ?Closure $onDataProgress = null,
    ): Collection
    {
        $strategyClass = $this->getStrategyClass();

        /** @var Strategy $strategy */
        $strategy = new $strategyClass(
            extractor: new Extractor(
                id: Str::uuid(),
                title: 'Title',
                outputInstructions: 'Output instructions',
                allowedTypes: [ExtractorFileType::IMAGES, ExtractorFileType::DOCUMENTS],
                llm: $this->model,
                schema: $this->schema,
                strategy: 'simple'
            ),
            onMessageProgress: $onMessageProgress,
            onMessage: $onMessage,
            onTokenStats: $onTokenStats,
            onActorTelemetry: $onActorTelemetry,
            onDataProgress: $onDataProgress,
        );

        $result = $strategy->run($this->artifacts);

        return $result->value;
    }

    public function send(): Collection
    {
        $prompt = $this->build();

        return collect($this->model->send($prompt));
    }
}
