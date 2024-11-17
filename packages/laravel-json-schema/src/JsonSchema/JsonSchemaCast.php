<?php

namespace Mateffy\JsonSchema;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Mateffy\JsonSchema;

class JsonSchemaCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return JsonSchema::fromJson($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!($value instanceof JsonSchema)) {
            throw new \InvalidArgumentException('Value must be an instance of JsonSchema');
        }

        return [
            $key => $value->toJson(),
        ];
    }
}
