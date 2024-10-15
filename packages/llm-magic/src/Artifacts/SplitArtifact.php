<?php

namespace Mateffy\Magic\Artifacts;

use Mateffy\Magic\Artifacts\Content\ImageContent;
use Mateffy\Magic\Artifacts\Content\TextContent;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;

/**
 * Artifact directory:
 * /artifacts/<ID>
 * /artifacts/<ID>/metadata.json
 * /artifacts/<ID>/source.<EXT>
 * /artifacts/<ID>/thumbnail.jpg
 * /artifacts/<ID>/embeds (optional)
 * /artifacts/<ID>/embeds/<FILENAME>.jpg
 */
readonly class SplitArtifact implements Artifact
{
    public function __construct(
        public Artifact $original,
        /** @var array<TextContent|ImageContent> */
        public array $contents,
        public int $tokens
    ) {}

    public function getMetadata(): ArtifactMetadata
    {
        return $this->original->getMetadata();
    }

    public function getSourcePath(): string
    {
        return $this->original->getSourcePath();
    }

    public function getSourceFilename(): string
    {
        return $this->original->getSourceFilename();
    }

    public function getContents(): array
    {
        return $this->contents;
    }

    public function getText(): ?string
    {
        return $this->original->getText();
    }

    public function getEmbedPath(string $filename): string
    {
        return $this->original->getEmbedPath($filename);
    }

    public function getBase64Image(ImageContent $filename): Base64Image
    {
        return $this->original->getBase64Image($filename);
    }

    /**
     * Splits the document. Adds data until either the character limit or embed limit is reached, then starts a new split.
     *
     * @return array<array<Artifact>>
     */
    public function split(int $maxTokens): array
    {
        // Splitting is not supported for split artifacts
        return [$this];
    }
}
