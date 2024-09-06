<?php

namespace App\Models\Concerns;

use Capevace\MagicImport\Prompt\TokenStats;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class TokenStatsCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        try {
            return ($value && ($json = json_decode($value, associative: true)))
                ? TokenStats::fromArray($json)
                : null;
        } catch (\Throwable $throwable) {
            report($throwable);

            return null;
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (! $value || ! ($value instanceof TokenStats)) {
            return null;
        }

        return json_encode($value->toArray());
    }
}
