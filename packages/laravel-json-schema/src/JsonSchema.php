<?php

namespace Mateffy;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Livewire\Wireable;
use Mateffy\JsonSchema\Concerns\CanBeCreated;
use Mateffy\JsonSchema\Concerns\CanBeTransformedToFilament;
use Mateffy\JsonSchema\Concerns\CanBeValidated;
use Mateffy\JsonSchema\Concerns\CanBeTransformed;
use Mateffy\JsonSchema\Concerns\CanBeWired;
use Mateffy\JsonSchema\Exceptions\InvalidType;
use Swaggest\JsonSchema\Exception;
use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

class JsonSchema implements Wireable
{
    public const VALID_TYPES = [
        'string',
        'number',
        'integer',
        'boolean',
        'array',
        'object',
        'null',
    ];

    public const VIRTUAL_TYPES = [
        'anyOf',
        'allOf',
        'oneOf',
        'not',
    ];

	use CanBeCreated;
    use CanBeTransformed;
    use CanBeTransformedToFilament;
    use CanBeValidated;
    use CanBeWired;

    protected array $type;
    protected SchemaContract $validator;
    protected array $schema;

    /**
     * Create a new JsonSchema instance.
     * The schema must contain a "type" property.
     *
     * This schema MUST be valid JSON Schema.
     * If you're building a JsonSchema object by nesting more JsonSchema objects,
     * use the `JsonSchema::from` method instead.
     *
     * @param array $schema A valid JSON schema. Cannot contain other JsonSchema instances.
     * @throws InvalidType
     * @throws Exception
     * @throws InvalidValue
     */
	public function __construct(array $schema)
	{
        // We always store the type as an array to simplify working with it.
        // When exporting the schema, we then convert it back to a string, if only one.
        if (($type = Arr::get($schema, 'type')) && !empty($type)) {
            if (is_array($type)) {
                $this->type = $type;
            } else {
                $this->type = [$type];
            }

            // Validate the types
            $invalidTypes = collect($this->type)
                ->reject(fn ($type) => in_array($type, static::VALID_TYPES));

            if ($invalidTypes->isNotEmpty()) {
                throw new InvalidType("Invalid type(s) in schema: {$invalidTypes->join(', ')}");
            }
        } else if (Arr::get($schema, 'anyOf')) {
            $this->type = ['anyOf'];
        } else if (Arr::get($schema, 'allOf')) {
            $this->type = ['allOf'];
        } else if (Arr::get($schema, 'oneOf')) {
            $this->type = ['oneOf'];
        } else if (Arr::get($schema, 'not')) {
            $this->type = ['not'];
        } else {
            throw new InvalidType('Schema must have a type');
        }

        $this->schema = $schema;

        $type = $this->type();
        $input = [...$this->schema];

        if (count($this->type()) > 0) {
            $input['type'] = $type;
        }

        $this->validator = Schema::import(json_decode(json_encode($input)));
    }

    public static function normalizeType(string|array $type, array $add = []): array
    {
        $types = is_array($type)
            ? $type
            : [$type];

        return collect([
            ...$types,
            ...$add
        ])
            ->unique()
            ->toArray();
    }

    protected static function processSchema(array $schema, bool $nullable): array
    {
        // Deep clone the schema
        // TODO: optimize this, serialization is expensive I think
        $modifiedSchema = unserialize(serialize(array_filter($schema)));

        if ($nullable) {
            $type = Arr::get($modifiedSchema, 'type');
            $type = is_array($type) ? $type : [$type];
            $containsNull = in_array('null', $type);

            if (!$containsNull) {
                $modifiedSchema['type'] = [...$type, 'null'];
            }
        }

        return $modifiedSchema;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->schema, $key, $default);
    }

    /**
     * Check if this schema satisfies the given type or types.
     * If the type is an array, it will check if this schema satisfies all of the types in the array.
     * Do not pass in full schemas, only types!
     *
     * @param string|array $type The type or types to check against. All must be satisfied.
     */
    public function satisfies(string $type): bool
    {
        return in_array($type, $this->type);
    }

    /**
     * @param string[] $type
     */
    public function satisfiesAll(array $type): bool
    {
        // Check that every type in the array is also in the type of this class
        return collect($type)
            ->every(fn ($type) => $this->satisfies($type));
    }

    /**
     * @param string[] $type
     */
    public function satisfiesAny(array $type): bool
    {
        // Check that some type in the array is also in the type of this class
        return collect($type)
            ->some(fn ($type) => $this->satisfies($type));
    }

    public function type(): array
    {
        return array_filter($this->type, fn ($type) => ! in_array($type, JsonSchema::VIRTUAL_TYPES));
    }

    /**
     * Check if this schema can be iterated over (arrays and objects).
     */
    public function isIterable(): bool
    {
        return $this->satisfies('array')
            || $this->satisfies('object')
            || $this->satisfies('anyOf')
            || $this->satisfies('allOf');
    }

    public function items(): ?JsonSchema
    {
        if (! $this->satisfies('array')) {
            return null;
        }

        if ($items = $this->schema['items'] ?? null) {
            return JsonSchema::from($items);
        }

        return null;
    }

    public function properties(): ?Collection
    {
        if (! $this->satisfies('object')) {
            return null;
        }

        if ($properties = $this->schema['properties'] ?? null) {
            return collect($properties);
        }

        return null;
    }
	// ... other methods ...

}
