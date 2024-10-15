<?php

namespace Mateffy\Magic\Config;

use Mateffy\Magic\LLM\LLM;
use Mateffy\Magic\LLM\MessageCollection;
use Mateffy\Magic\LLM\ModelLaunchInterface;
use Mateffy\Magic\Prompt\Prompt;
use Closure;

readonly class Extractor implements ModelLaunchInterface
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $outputInstructions,
        /** @var ExtractorFileType[] $allowedTypes */
        public array $allowedTypes,
        public LLM $llm,
        public array $schema,
        public string $strategy,
    ) {}

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null, ?Closure $onTokenStats = null): MessageCollection
    {
        return $this->llm->stream($prompt, $onMessageProgress, $onMessage, $onTokenStats);
    }

    public function send(Prompt $prompt): MessageCollection
    {
        return $this->llm->send($prompt);
    }
}
