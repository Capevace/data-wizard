<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Mateffy\Magic\Artifacts\ArtifactGenerationStatus;
use Mateffy\Magic\Artifacts\LocalArtifact;

/**
 * @property ArtifactGenerationStatus $artifact_status
 * @property-read string $thumbnail_src
 * @property-read LocalArtifact|null $artifact
 * @property-read string $artifact_path
 */
class File extends \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    use UsesUuid;

    public function getCasts()
    {
        return [
            ...parent::getCasts(),
            'artifact_status' => ArtifactGenerationStatus::class,
        ];
    }

    public function getThumbnailSrcAttribute(): string
    {
        return $this->getUrl('thumbnail');
    }

    public function getArtifactPathAttribute(): string
    {
        return str($this->getPath(''))
            ->beforeLast('/')
            ->append('/artifact');
    }

    /**
     * @throws \JsonException
     */
    public function getArtifactAttribute(): ?LocalArtifact
    {
        return $this->artifact_status === ArtifactGenerationStatus::Complete
            ? LocalArtifact::fromPath($this->artifact_path)
            : null;
    }
}
