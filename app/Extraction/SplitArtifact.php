<?php

namespace App\Extraction;

use Illuminate\Support\Collection;
use App\Extraction\Artifacts\Slices\EmbedSlice;
use App\Extraction\Artifacts\Slices\Slice;
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
        /** @var array<Slice> */
        public array $contents,
        public int $tokens
    ) {}

    public function getMetadata(): ArtifactMetadata
    {
        return $this->original->getMetadata();
    }

    public function getContents(): array
    {
        return $this->contents;
    }

    public function getText(): ?string
    {
        return $this->original->getText();
    }

    public function makeBase64Image(EmbedSlice $content): Base64Image
    {
        return $this->original->makeBase64Image($content);
    }

    /**
     * Splits the document. Adds data until either the character limit or embed limit is reached, then starts a new split.
     *
     * @return Artifact
	 */
    public function split(int $maxTokens): array
    {
        // Splitting is not supported for split artifacts
        return [$this];
    }

    public function getBase64Images(?int $maxPages = null): Collection
    {
        return collect($this->getContents())
            ->filter(fn (Slice $content) => $content instanceof EmbedSlice)
            ->groupBy(fn (EmbedSlice $content) => $content->getPage() ?? 0)
            ->sortBy(fn (Collection $contents, $page) => $page)
            ->take($maxPages)
            ->flatMap(fn (Collection $contents) => collect($contents)
                ->map(fn (EmbedSlice $image) => $this->makeBase64Image($image))
            );
    }

    public function getEmbedContents(EmbedSlice $content): mixed
    {
        return $this->original->getEmbedContents($content);
    }
}
