<?php

namespace Mateffy\Magic\Artifacts;

use Mateffy\Magic\Artifacts\Content\ImageContent;
use Mateffy\Magic\Artifacts\Content\TextContent;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;

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
    public function getBase64Image(ImageContent $filename): Base64Image;

    /**
     * @return {0: array<Artifact>, 1: int}
     */
    public function split(int $maxTokens): array;
}
