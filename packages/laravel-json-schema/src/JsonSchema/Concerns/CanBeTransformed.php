<?php

namespace Mateffy\JsonSchema\Concerns;

use Mateffy\JsonSchema;

trait CanBeTransformed
{
	public function toArray(): array
	{
        $type = $this->type();
        $type = count($type) > 1
            ? $type
            : $type[0];

		return array_filter([
            ...$this->schema,
            'type' => $type,
        ]);
	}

	public function toJson($flags = 0): string
	{
		return json_encode($this->toArray(), $flags);
	}

    public static function convertToValidatorRules(JsonSchema|array $schema, ?string $prefix = null): array
    {
        if (is_array($schema)) {
            $schema = JsonSchema::from($schema);
        }

        if ($schema->satisfies('object')) {
            return collect($schema->toArray()['properties'] ?? [])
                ->mapWithKeys(function (array $property, string $key) use ($prefix) {
                    $path = implode('.', array_filter([$prefix, $key]));

                    return static::convertToValidatorRules($property, $path);
                })
                ->all();
        }

        if ($schema->satisfies('array')) {
            $path = implode('.', array_filter([$prefix, '*']));

            return static::convertToValidatorRules($schema->items(), $path);
        }

        return [$prefix => []];
    }
}
