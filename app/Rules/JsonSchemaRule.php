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
            JsonSchema::import($value);
        } catch (\Exception $e) {
            $fail($e->getMessage());
        }
	}
}
