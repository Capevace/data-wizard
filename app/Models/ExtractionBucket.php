<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use App\Models\ExtractionBucket\BucketCreationSource;
use App\Models\ExtractionBucket\BucketStatus;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Mateffy\Magic\Functions\ExtractData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Mateffy\Magic\Buckets\CloudArtifact;

/**
 * @property-read array $target_schema
 */
class ExtractionBucket extends Model implements HasMedia
{
    use HasFactory, SoftDeletes;
    use InteractsWithMedia;
    use UsesUuid;

    protected $fillable = [
        'description',
        'created_by_id',
        'status',
        'started_at',
        'extractor_id',
    ];

    protected $casts = [
        'started_at' => 'timestamp',
        'status' => BucketStatus::class,
        'created_using' => BucketCreationSource::class,
    ];

    public function getAttributes()
    {
        return [
            'status' => BucketStatus::Pending,
            'started_at' => now()->toImmutable(),
            'extractor_id' => 'default',
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

    public function cloud_artifacts(): HasMany
    {
        return $this->hasMany(CloudArtifact::class, 'bucket_id');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ExtractionRun::class, 'bucket_id');
    }

    public function getTargetSchemaAttribute(): ?array
    {
        return [
            'type' => 'object',
            'properties' => [
                'estates' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'buildings' => (new ExtractData)->schema(),
                        ],
                    ],
                ],
            ],
        ];
    }

    public function logUsage(): void
    {
        $this->updated_at = now();
        $this->save();
    }

    public static function createEmbedded(?string $externalId = null): self
    {
        return self::create([
            'description' => "Embedded extraction bucket",

        ]);
    }
}
