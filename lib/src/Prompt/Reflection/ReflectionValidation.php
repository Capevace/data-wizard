<?php

namespace Capevace\MagicImport\Prompt\Reflection;

use Exception;
use Illuminate\Support\Facades\Validator;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class ReflectionValidation
{
    protected ?ReflectionClass $reflection;

    public function __construct(protected string $classpath)
    {
        try {
            $this->reflection = new ReflectionClass($this->classpath);
        } catch (\ReflectionException $e) {
            // Log the error
            dd("Failed to create ReflectionClass for {$this->classpath}: ".$e->getMessage());
            $this->reflection = null;
        }
    }

    public function validate(array $data): ?array
    {
        if (! $this->reflection) {
            throw new Exception("Failed to retrieve the reflection object for {$this->classpath}");
        }

        $rules = $this->generateValidationRules();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        return null;
    }

    protected function generateValidationRules(): array
    {
        $rules = [];

        try {
            foreach ($this->reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                $propertyRules = $this->getPropertyRules($property);
                if (! empty($propertyRules)) {
                    $rules[$property->getName()] = $propertyRules;
                }
            }
        } catch (\Exception $e) {
            // Log the error
            dd('Error generating validation rules: '.$e->getMessage());
        }

        return $rules;
    }

    protected function getPropertyRules(ReflectionProperty $property): array
    {
        $rules = [];
        $type = $property->getType();

        if ($type instanceof ReflectionNamedType) {
            $rules = $this->getRulesForNamedType($type);
        } elseif ($type instanceof ReflectionUnionType) {
            $rules = $this->getRulesForUnionType($type);
        }

        // Add 'required' rule if the property is not nullable
        if ($type && ! $type->allowsNull()) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        $rules = array_merge($rules, $this->getAttributeRules($property));

        // Handle nested structures
        $arrayOfAttribute = $this->getArrayOfAttribute($property);
        if ($arrayOfAttribute) {
            $rules = $this->handleArrayOfRules($rules, $arrayOfAttribute);
        }

        return $rules;
    }

    protected function getRulesForNamedType(ReflectionNamedType $type): array
    {
        $typeName = $type->getName();

        if (enum_exists($typeName)) {
            return ['string', 'in:'.implode(',', array_map(fn ($case) => $case->value, $typeName::cases()))];
        }

        return match ($typeName) {
            'int' => ['integer'],
            'float' => ['numeric'],
            'string' => ['string'],
            'bool' => ['boolean'],
            'array' => ['array'],
            default => class_exists($typeName) ? ['array'] : ['string'],
        };
    }

    protected function getRulesForUnionType(ReflectionUnionType $type): array
    {
        $typeNames = array_map(fn ($t) => $t->getName(), $type->getTypes());
        $basicTypes = ['integer', 'numeric', 'string', 'boolean', 'array'];
        $validTypes = array_intersect($typeNames, $basicTypes);

        return empty($validTypes) ? ['string'] : $validTypes;
    }

    protected function getAttributeRules(ReflectionProperty $property): array
    {
        $rules = [];

        foreach ($property->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof PromptReflectionAttribute) {
                $rules = array_merge($rules, $instance->getValidationRules());
            }
        }

        return $rules;
    }

    protected function getArrayOfAttribute(ReflectionProperty $property): ?ArrayOf
    {
        try {
            $arrayOfAttributes = $property->getAttributes(ArrayOf::class);
            if (empty($arrayOfAttributes)) {
                return null;
            }

            return $arrayOfAttributes[0]->newInstance();
        } catch (\Throwable $e) {
            // Log the error
            dd('Error getting ArrayOf attribute: '.$e->getMessage());

            return null;
        }
    }

    protected function handleArrayOfRules(array $rules, ArrayOf $arrayOf): array
    {
        $rules[] = 'array';

        $type = $arrayOf->type;
        if (class_exists($type)) {
            $nestedValidator = new self($type);
            $nestedRules = $nestedValidator->generateValidationRules();

            $rules[] = function ($attribute, $value, $fail) use ($nestedRules) {
                foreach ($value as $index => $item) {
                    $itemValidator = Validator::make($item, $nestedRules);
                    if ($itemValidator->fails()) {
                        foreach ($itemValidator->errors()->all() as $error) {
                            $fail("$attribute.$index: $error");
                        }
                    }
                }
            };
        } else {
            $nestedRules = $this->getRulesForNamedType(new ReflectionNamedType($type));
            if (! empty($nestedRules)) {
                $rules[] = 'array:*'.implode('|', $nestedRules);
            }
        }

        return $rules;
    }
}
