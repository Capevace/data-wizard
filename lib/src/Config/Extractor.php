<?php

namespace Capevace\MagicImport\Config;

use Capevace\MagicImport\LLM\LLM;
use Illuminate\Support\Collection;
use Swaggest\JsonSchema\JsonSchema;
use Swaggest\JsonSchema\SchemaContract;

readonly class Extractor
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $description,
        /** @var ExtractorFileType[] $allowedTypes */
        public array $allowedTypes,
        public LLM $llm,
        public SchemaContract $schema,
        public string $strategy,
    )
    {
    }
}
