<?php

namespace Capevace\MagicImport\Builder;

use Capevace\MagicImport\Builder\Concerns\HasArtifacts;
use Capevace\MagicImport\Builder\Concerns\HasModel;
use Capevace\MagicImport\Builder\Concerns\HasSchema;
use Capevace\MagicImport\Builder\Concerns\HasSystemPrompt;
use Capevace\MagicImport\Builder\Concerns\HasTools;
use Capevace\MagicImport\Builder\Concerns\LaunchesBuilderLLM;
use Capevace\MagicImport\Config\Extractor;
use Capevace\MagicImport\Config\ExtractorFileType;
use Capevace\MagicImport\Prompt\ExtractorPrompt;
use Capevace\MagicImport\Prompt\Prompt;
use Closure;
use Illuminate\Support\Str;

class ExtractionLLMBuilder implements LLMBuilder
{
    use HasArtifacts;
    use HasModel;
    use HasSchema;
    use HasSystemPrompt;
    use HasTools;
    use LaunchesBuilderLLM;

    public function build(): Prompt
    {
        $extractor = new Extractor(
            id: Str::uuid(),
            title: 'Title',
            outputInstructions: 'Output instructions',
            allowedTypes: [ExtractorFileType::IMAGES, ExtractorFileType::DOCUMENTS],
            llm: $this->model,
            schema: $this->schema,
            strategy: 'simple'
        );

        $prompt = new ExtractorPrompt(
            extractor: $extractor,
            artifacts: [],
        );

        return $prompt;
    }

    public function stream(?Closure $onMessageProgress = null, ?Closure $onMessage = null): array
    {
        $prompt = $this->build();

        return $this->model->stream($prompt, $onMessageProgress, $onMessage);
    }

    public function send(): array
    {
        $prompt = $this->build();

        return $this->model->send($prompt);
    }
}
