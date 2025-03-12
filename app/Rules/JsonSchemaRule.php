<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Swaggest\JsonSchema\JsonSchema;

class JsonSchemaRule implements ValidationRule
{
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		try {
            if (is_string($value)) {
                $value = json_decode($value);
            }

            JsonSchema::import($value);
        } catch (\Exception $e) {
            $fail($e->getMessage());
        }
	}
}
