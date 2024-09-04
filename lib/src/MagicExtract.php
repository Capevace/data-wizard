<?php

namespace Capevace\MagicImport;

use Capevace\MagicImport\Config\Extractor;
use Capevace\MagicImport\Config\ExtractorFileType;
use Capevace\MagicImport\LLM\ElElEm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Swaggest\JsonSchema\JsonSchema;
use Swaggest\JsonSchema\Schema;

class MagicExtract
{
    public const DEFAULT_STRATEGY = 'simple';

    protected array $extractors = [];

    public function boot()
    {
        $config = config('magic-import.extractors', []);

        $this->setupWithConfig($config);
    }

    public function setupWithConfig(array $config): void
    {
        $extractors = Arr::get($config, 'extractors', []);

        foreach ($extractors as $key => $extractor) {
            $this->registerExtractor($key, $extractor);
        }
    }

    public function find(string $extractor): ?Extractor
    {
        return $this->extractors[$extractor] ?? null;
    }

    /**
     * Register an extractor to be available for use.
     * This makes the library usable both as a strongly typed library
     * and as JSON configurable e.g. in a Docker environment.
     */
    public function registerExtractor(string $id, array|Extractor $extractor): Extractor
    {
        if ($extractor instanceof Extractor) {
            return $this->extractors[$id] = $extractor;
        }

        /** @var Collection<ExtractorFileType> $types */
        $types = collect(Arr::get($extractor, 'files.allowedTypes', []))
            ->map(fn(string|array $type) => is_string($type)
                ? ExtractorFileType::fromString($type)
                : ExtractorFileType::fromArray($type)
            )
            ->filter();

        if ($types->isEmpty()) {
            throw new \InvalidArgumentException("Extractor {$id} needs at least one allowed type");
        }

        $llm = Arr::get($extractor, 'model');

        if (!$llm) {
            throw new \InvalidArgumentException("Extractor {$id} is missing a model");
        }

        $outputFormat = Arr::get($extractor, 'output.format', []);

        if (count($outputFormat) === 0) {
            throw new \InvalidArgumentException("Extractor {$id} is missing an output schema");
        }

        // We wrap the output format in an object schema here, so the config can be more concise.
        // Also, this simplifies things, as we're always gonna be getting an object response from the LLM.
        $schema = [
            'type' => 'object',
            'properties' => $outputFormat,
            'required' => array_keys($outputFormat),
        ];

        return $this->extractors[$id] = new Extractor(
            id: $id,
            title: Arr::get($extractor, 'title', $id),
            outputInstructions: Arr::get($extractor, 'description'),
            allowedTypes: $types->all(),
            llm: is_string($llm)
                ? ElElEm::fromString($llm)
                : ElElEm::fromArray($llm),
            schema: JsonSchema::import($schema),
            strategy: Arr::get($extractor, 'strategy', self::DEFAULT_STRATEGY),
        );
    }

    public function extract(
        Extractor $extractor,
    )
    {

    }
}






















