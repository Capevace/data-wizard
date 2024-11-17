<?php

namespace Mateffy\Magic\Buckets;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CloudArtifactChunk extends Model implements HasMedia
{
    use HasUuids, HasUuids, HasUuids, SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'type',
        'text',
        'page',
        'tokens',
        'cloud_artifact_id',
    ];

    public function cloud_artifact(): BelongsTo
    {
        return $this->belongsTo(CloudArtifact::class);
    }

    public function original()
    {
        return $this->getFirstMedia();
    }
}
