<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Mateffy\Magic\Extraction\Artifacts\Artifact;
use Mateffy\Magic\Extraction\Artifacts\DiskArtifact;

/**
 * @property ArtifactGenerationStatus $artifact_status
 * @property-read string $thumbnail_src
 * @property-read Artifact|null $artifact
 * @property-read string $artifact_path
 */
class File extends \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    use UsesUuid;

    public function getHidden()
    {
        return [
            ...parent::getHidden(),
            'temporary_upload',
            'temporaryUpload',
        ];
    }

    public function getCasts()
    {
        return [
            ...parent::getCasts(),
            'artifact_status' => ArtifactGenerationStatus::class,
        ];
    }

    public function getThumbnailSrcAttribute(): string
    {
        return URL::temporarySignedRoute(
            name: 'files.thumbnail',
            expiration: now()->addDay(),
            parameters: ['fileId' => $this->id]
        );
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
    public function getArtifactAttribute(): ?Artifact
    {
        return DiskArtifact::from(path: $this->getOriginalPath(), disk: $this->getOriginalDisk());
    }

    public function getOriginalPath(): string
    {
        return $this->getPathRelativeToRoot();
    }

    public function getOriginalDisk(): string
    {
        return $this->disk;
    }

    public function getSignedEmbedUrl(string $path): string
    {
        return URL::signedRoute(
            name: 'files.contents',
            parameters: [
                'fileId' => $this->id,
                'path' => base64_encode($path),
                'debug_path' => $path
            ],
            expiration: now()->addDay()
        );
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'size' => $this->size,
            'mime_type' => $this->mime_type,
            'url' => $this->getUrl(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'thumbnail_src' => $this->thumbnail_src,
            'artifact_path' => $this->artifact_path,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function (File $file) {
            $artifact = $file->artifact;

            if ($artifact instanceof DiskArtifact) {
                Storage::disk($artifact->artifactDirDisk)
                    ->deleteDirectory($artifact->artifactDir);
            }
        });
    }
}
