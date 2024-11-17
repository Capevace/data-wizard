<?php

namespace Mateffy\Magic\Buckets;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Mateffy\Magic\Artifacts\Artifact;
use Mateffy\Magic\Artifacts\ArtifactGenerationStatus;
use Mateffy\Magic\Artifacts\ArtifactMetadata;
use Mateffy\Magic\Artifacts\Content\ImageContent;
use Mateffy\Magic\Artifacts\Content\TextContent;
use Mateffy\Magic\LLM\Message\MultimodalMessage\Base64Image;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property string $name
 * @property string $extension
 * @property string $mime_type
 * @property ArtifactGenerationStatus $status
 * @property int $size
 * @property string|null $ai_summary
 *
 */
class CloudArtifact extends Model implements HasMedia, Artifact
{
    use HasUuids, SoftDeletes;

    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'extension',
        'mime_type',
        'status',
        'size',
        'ai_summary',
    ];
    protected $casts = [
        'status' => ArtifactGenerationStatus::class,
        'size' => 'integer',
    ];

    protected $attributes = [
        'status' => ArtifactGenerationStatus::Pending,
    ];

    public function chunks(): HasMany
    {
        return $this->hasMany(CloudArtifactChunk::class);
    }

    public function original()
    {
        return $this->getFirstMedia();
    }

    public function file(): ChatFile
    {
        return ChatFile::fromCloudArtifact($this);
    }

    public function streamTo(string $path): void
    {
        $stream = $this->original()->stream();
        file_put_contents($path, $stream);
    }

    public function getContents(): array
    {
        return $this->chunks()
            ->orderBy('page')
            ->get()
            ->map(function (CloudArtifactChunk $chunk) {
                return match ($chunk->type) {
                    'text' => new TextContent($chunk->text, page: $chunk->page),
                    'image', 'page-image' => new ImageContent(path: $chunk->id, mimetype: $chunk->original()->mime_type),
                    default => null
                };
            })
            ->filter()
            ->all();
    }

    public function getText(): ?string
    {
        return $this->chunks()
            ->where('type', 'text')
            ->orderBy('page')
            ->get()
            ->map(fn(CloudArtifactChunk $chunk) => $chunk->text)
            ->implode("\n");
    }

    public function getMetadata(): ArtifactMetadata
    {
        return new ArtifactMetadata(
            name: $this->name,
            mimetype: $this->mime_type,
            extension: $this->extension,
        );
    }

    public function getSourcePath(): string
    {
        return $this->original()->getPath();
    }

    public function getEmbedPath(string $filename): string
    {
        return $this->chunks()->find($filename)
            ?->original()
            ?->getPath() ?? '';
    }

    public function getBase64Image(ImageContent $filename): Base64Image
    {
        /** @var ?Media $media */
        $media = $this->chunks()->find($filename->path)?->original();

        if (! $media) {
            return new Base64Image(base64_encode(''), mime: $filename->mimetype);
        }

        return new Base64Image($media->getPath(), mime: $filename->mimetype);
    }

    public function split(int $maxTokens): array
    {
        $tokens = 0;

        return $this->chunks()
            ->orderBy('page')
            ->get()
            ->chunkWhile(function (CloudArtifactChunk $chunk) use (&$tokens, $maxTokens) {
                $tokens += strlen($chunk->text);

                return $tokens <= $maxTokens;
            })
            ->map(fn(Collection $chunks) => $chunks
                ->map(function (CloudArtifactChunk $chunk) {
                    return match ($chunk->type) {
                        'text' => new TextContent($chunk->text, page: $chunk->page),
                        'image', 'page-image' => new ImageContent(path: $chunk->id, mimetype: $chunk->original()?->mime_type),
                        default => null
                    };
                })
                ->filter()
            )
            ->all();
    }

    public function scopeWhereFilename($query, string $filename): void
    {
        $query->where('name', pathinfo($filename, PATHINFO_FILENAME))
            ->where('extension', pathinfo($filename, PATHINFO_EXTENSION));
    }
}
