<?php

namespace App\Models;

use Akaunting\Money\Money;
use App\Models\Concerns\JsonSchemaCast;
use App\Models\Concerns\TokenStatsCast;
use App\Models\Concerns\UsesUuid;
use App\Models\ExtractionRun\RunStatus;
use Capevace\MagicImport\Prompt\TokenStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

/**
 * @property-read User $started_by
 * @property ?array $target_schema
 * @property ?SchemaContract $target_schema_typed
 * @property ?array $result_json
 * @property ?string $partial_result_json
 * @property ?array $error
 * @property ?TokenStats $token_stats
 * @property string $strategy
 * @property ?string $saved_extractor_id
 * @property-read ?SavedExtractor $saved_extractor
 */
class ExtractionRun extends Model
{
    use UsesUuid;
    use HasFactory;

    protected $fillable = [
        'target_schema',
        'strategy',
        'status',
        'error',
        'started_by_id',
        'result_json',
        'partial_result_json',
        'token_stats',
        'saved_extractor_id'
    ];

    protected $casts = [
        'target_schema' => 'json',
        'error' => 'json',
        'status' => RunStatus::class,
        'result_json' => 'json',
        'token_stats' => TokenStatsCast::class,
    ];

    protected $attributes = [
        'target_schema' => null,
        'error' => null,
        'status' => RunStatus::Pending,
        'result_json' => '{}',
        'token_stats' => null,
    ];

    public function bucket(): BelongsTo
    {
        return $this->belongsTo(ExtractionBucket::class, 'bucket_id');
    }

    public function started_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by_id');
    }

    public function getTargetSchemaTypedAttribute(): ?SchemaContract
    {
        return Schema::import(
            json_decode(json_encode($this->target_schema))
        );
    }

    public function saved_extractor(): BelongsTo
    {
        return $this->belongsTo(SavedExtractor::class, 'saved_extractor_id');
    }

    public function calculateTotalCost(): Money
    {
        if (!$this->token_stats) {
            return Money::EUR(0);
        }

        return $this->token_stats->calculateTotalCost();
    }

    public function actors(): HasMany
    {
        return $this->hasMany(Actor::class, 'extraction_run_id');
    }
}
