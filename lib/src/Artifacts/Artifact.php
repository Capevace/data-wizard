<?php

namespace Capevace\MagicImport\Artifacts;

use Capevace\MagicImport\Artifacts\Content\ImageContent;
use Capevace\MagicImport\Artifacts\Content\TextContent;
use Capevace\MagicImport\Config\ExtractorFileType;

interface Artifact
{
    /**
     * @return array<TextContent|ImageContent>
     */
    public function getContents(): array;
    public function getMetadata(): ArtifactMetadata;
    public function getSourcePath(): string;

    public function getEmbedPath(string $filename): string;
    public function split(int $maxCharacters, int $maxEmbedCount): array;
}