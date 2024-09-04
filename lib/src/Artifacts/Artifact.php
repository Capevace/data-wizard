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
    public function getText(): ?string;

    public function getMetadata(): ArtifactMetadata;
    public function getSourcePath(): string;

    public function getEmbedPath(string $filename): string;

    /**
     * @return {0: array<Artifact>, 1: int}
     */
    public function split(int $maxTokens): array;
}
