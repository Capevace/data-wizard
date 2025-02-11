<?php

namespace App\Extraction;

use Illuminate\Support\Collection;
use App\Extraction\Artifacts\Slices\EmbedSlice;
use App\Extraction\Artifacts\Slices\Slice;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;

interface Artifact
{
    public function getMetadata(): ArtifactMetadata;

    /**
     * @return array<Slice>
     */
    public function getContents(): array;

    public function getText(): ?string;
    public function getBase64Images(?int $maxPages = null): Collection;
    public function getEmbedContents(EmbedSlice $content): mixed;
    public function makeBase64Image(EmbedSlice $content): Base64Image;

    /**
     * @return {0: array<Artifact>, 1: int}
     */
    public function split(int $maxTokens): array;
}
