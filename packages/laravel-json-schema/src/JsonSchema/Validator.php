<?php

namespace Mateffy\JsonSchema;

use Illuminate\Translation\Translator;
use Mateffy\JsonSchema;

class Validator extends \Illuminate\Validation\Validator
{
    public static function fromJsonSchema(JsonSchema $schema, array $errors = []): \Illuminate\Contracts\Validation\Factory|\Illuminate\Contracts\Validation\Validator|\Illuminate\Foundation\Application
    {
        $rules = JsonSchema::convertToValidatorRules($schema);

        $validator = validator(
            data: [],
            rules: $rules
        );

        foreach ($errors as $key => $error) {
            $validator->errors()->add($key, $error);
        }

        return $validator;
    }


}
