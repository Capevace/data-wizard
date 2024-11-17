<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $title
 * @property array $data
 * @property-read SmartCollection $smart_collection
 */
class SmartCollectionItem extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'data',
        'smart_collection_id',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function smart_collection(): BelongsTo
    {
        return $this->belongsTo(SmartCollection::class, 'smart_collection_id');
    }
}
