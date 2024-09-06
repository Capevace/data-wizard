<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Swaggest\JsonSchema\JsonSchema;

/**
 * @property string $strategy
 * @property ?string $label
 * @property bool $was_automatically_created
 * @property array $json_schema
 * @property ?string $output_instructions
 * @property-read ?\Carbon\CarbonImmutable $last_ran_at
 * @property-read ?JsonSchema $typed_schema
 */
class SavedExtractor extends Model
{
    use SoftDeletes;
    use UsesUuid;

    protected $fillable = [
        'strategy',
        'label',
        'was_automatically_created',
        'json_schema',
        'output_instructions',
    ];

    protected $casts = [
        'json_schema' => 'json',
    ];

    public function runs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExtractionRun::class);
    }

    public function getLastRanAtAttribute(): ?\Carbon\CarbonImmutable
    {
        return $this->runs()->latest()->first()?->started_at;
    }

    public function getTypedSchemaAttribute(): ?JsonSchema
    {
        try {
            return JsonSchema::import($this->json_schema);
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public function logUsage(): void
    {
        $this->updated_at = now()->toImmutable();
        $this->save();
    }
}
