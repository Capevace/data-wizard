<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use JsonException;
use Mateffy\JsonSchema;
use Mateffy\JsonSchema\JsonSchemaCast;

/**
 * @property JsonSchema $json_schema
 * @property array $json_schema_array
 * @property string $json_schema_string
 */
class SmartCollection extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'icon',
        'color',
        'json_schema',
        'json_schema_array',
        'json_schema_json',
    ];

    protected $casts = [
        'json_schema' => JsonSchemaCast::class
    ];

    protected $appends = [
        'json_schema_array',
        'json_schema_string',
    ];

    public function getJsonSchemaArrayAttribute(): array
    {
        return $this->json_schema->toArray();
    }

    public function setJsonSchemaArrayAttribute(array $schema): void
    {
        $this->json_schema = JsonSchema::from($schema);
    }

    public function getJsonSchemaStringAttribute(): string
    {
        return $this->json_schema->toJson(flags: JSON_PRETTY_PRINT);
    }

    /**
     * @throws JsonException
     */
    public function setJsonSchemaStringAttribute(string $schema): void
    {
        $json = json_decode($schema, true, flags: JSON_THROW_ON_ERROR);

        $this->json_schema = JsonSchema::from($json);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SmartCollectionItem::class);
    }
}
