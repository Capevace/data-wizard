<?php

namespace App\Models;

use ApiPlatform\Laravel\Eloquent\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Livewire\Components\EmbeddedExtractor;
use App\Models\Concerns\UsesUuid;
use App\Models\ExtractionBucket\BucketCreationSource;
use App\Models\ExtractionBucket\BucketStatus;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mateffy\Magic\Extraction\Artifacts\Artifact;
use Mateffy\Magic\Extraction\ContextOptions;
use Mateffy\Magic\Extraction\Slices\Slice;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property string $description
 * @property ?string $created_by_id
 * @property ?string $extractor_id
 * @property BucketCreationSource $created_using
 * @property-read User $created_by
 * @property-read Collection<Artifact> $artifacts
 * @property-read Collection<File> $files
 */
#[ApiResource(
    uriTemplate: '/buckets',
    operations: [
        new GetCollection,
        new Post()
    ],
    rules: [
        'description' => ['required'],
    ],
    middleware: ['auth:sanctum']
)]
#[ApiResource(
    uriTemplate: '/buckets/{id}',
    operations: [
        new Get,
        new Delete
    ],
    defaults: [
        'created_using' => BucketCreationSource::Api,
    ],
    rules: [
        'description' => ['required'],
    ],
    middleware: ['auth:sanctum']
)]
#[QueryParameter(key: 'model', filter: PartialSearchFilter::class)]
#[QueryParameter(key: 'output_instructions', filter: PartialSearchFilter::class)]
class ExtractionBucket extends Model implements HasMedia
{
    use HasFactory, SoftDeletes;
    use InteractsWithMedia;
    use UsesUuid;

    protected $fillable = [
        'description',
        'created_by_id',
        'created_using',
    ];

    protected $casts = [
        'created_using' => BucketCreationSource::class,
    ];

    public function getAttributes()
    {
        return [
            'created_by_id' => auth()->user()?->id,
            'created_using' => BucketCreationSource::App,
            ...parent::getAttributes(),
        ];
    }

    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('files')
            ->useDisk('public')
            ->storeConversionsOnDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->fit(Fit::Contain, desiredWidth: 400, desiredHeight: 300);
    }

    public function files(): MorphMany
    {
        return $this->media();
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ExtractionRun::class, 'bucket_id');
    }

    public function getArtifactsAttribute(): Collection
    {
        $this->loadMissing('files');

        return $this->files
            ->filter(fn (File $file) => $file->artifact && $file->artifact_status === ArtifactGenerationStatus::Complete)
            ->sortBy(fn (File $file) => $file->artifact->getMetadata()->name)
            ->values()
            ->map(fn (File $file) => $file->artifact);
    }

    public function calculateInputTokens(?ContextOptions $contextOptions): int
    {
        return $this->artifacts
            ->map(fn (Artifact $artifact) => $artifact->getContents(contextOptions: $contextOptions)
                ->reduce(fn (int $tokens, Slice $slice) => $tokens + $slice->getTokens(), 0)
            )
            ->sum();
    }

    public static function createEmbedded(?string $externalId = null): self
    {
        return self::create([
            'description' => "Embedded extraction bucket",
            'created_by_id' => auth()->user()?->id,
            'created_using' => BucketCreationSource::Embed,
        ]);
    }
}
