<?php

namespace Capevace\MagicImport\Config;

use Capevace\MagicImport\LLM\LLM;

readonly class Extractor
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
}
