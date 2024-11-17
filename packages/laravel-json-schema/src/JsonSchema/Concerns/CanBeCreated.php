<?php

namespace Mateffy\JsonSchema\Concerns;

trait CanBeCreated
{
	public static function fromPath(string $path): static
	{
		throw new \Exception('Not implemented');
	}

	public static function fromUrl(string $url): static
	{
		throw new \Exception('Not implemented');
	}

	public static function from($schema, ?bool $nullable = false): static
	{
        $processed = self::processSchema($schema, $nullable);

		return app(static::class, [
            'schema' => $processed,
        ]);
	}

    public static function fromJson(string $json): static
    {
        $schema = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        return self::from($schema);
    }

	public static function fromFilamentForm(array $formSchema): static
	{
		// Implementation
	}

	public static function fromFilamentTable(array $tableSchema): static
	{
		// Implementation
	}

	public static function object(array $properties, array $required = [], string $description = null): static
	{
        return self::from([
            'type' => 'object',
            'properties' => $properties,
            'required' => $required,
            'description' => $description,
        ]);
	}

	public static function array(
        array|self $schema,
        ?string $description = null,
        ?int $minItems = null,
        ?int $maxItems = null,
    ): static
	{
        if (is_array($schema)) {
            $schema = self::from($schema);
        }

		return self::from([
            'type' => 'array',
            'items' => $schema->toArray(),
            'description' => $description,
            'minItems' => $minItems,
            'maxItems' => $maxItems,
        ]);
	}

	public static function string(
        ?string $description = null,
        ?string $format = null,
        ?int $minLength = null,
        ?int $maxLength = null,
        ?string $pattern = null,
    ): static
	{
		return self::from([
            'type' => 'string',
            'description' => $description,
            'format' => $format,
            'minLength' => $minLength,
            'maxLength' => $maxLength,
            'pattern' => $pattern
        ]);
	}

	public static function float(
        ?string $description = null,
        ?int $minimum = null,
        ?int $maximum = null,
        float|int|null $multipleOf = null,
    ): static
	{
		return self::from([
            'type' => 'number',
            'description' => $description,
            'minimum' => $minimum,
            'maximum' => $maximum,
            'multipleOf' => $multipleOf,
        ]);
	}

	public static function integer(
        ?string $description = null,
        ?int $minimum = null,
        ?int $maximum = null,
        float|int|null $multipleOf = null,
    ): static
	{
		return self::from([
            'type' => 'integer',
            'description' => $description,
            'minimum' => $minimum,
            'maximum' => $maximum,
            'multipleOf' => $multipleOf,
        ]);
	}

	public static function boolean(?string $description = null): static
	{
		return self::from([
            'type' => 'boolean',
            'description' => $description,
        ]);
	}

	public static function null(?string $description = null): static
	{
		return self::from([
            'type' => 'null',
            'description' => $description,
        ]);
	}
}
