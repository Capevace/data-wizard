<?php

namespace Capevace\MagicImport\Prompt\Reflection;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;

class ReflectionFill
{
    protected ReflectionClass $reflection;

    public function __construct(protected string $classpath)
    {
        $this->reflection = new ReflectionClass($this->classpath);
    }

    public function fill(array $data): object
    {
        $constructor = $this->reflection->getConstructor();
        $parameters = $constructor ? $constructor->getParameters() : [];
        $args = [];

        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();
            if (array_key_exists($paramName, $data)) {
                $args[] = $this->castValue($data[$paramName], $parameter);
            } else {
                $args[] = $this->getDefaultValue($parameter);
            }
        }

        $instance = $this->reflection->newInstanceArgs($args);

        foreach ($this->reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            if (! $property->isInitialized($instance) && ! array_key_exists($propertyName, $data)) {
                $this->handleUnsetProperty($instance, $property);
            }
        }

        return $instance;
    }

    private function castValue($value, ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if ($type instanceof ReflectionNamedType) {
            return $this->castToType($value, $type->getName(), $type->allowsNull());
        } elseif ($type instanceof ReflectionUnionType) {
            return $this->castToUnionType($value, $type);
        }

        return $value;
    }

    private function getDefaultValue(ReflectionParameter $parameter)
    {
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        $type = $parameter->getType();
        if ($type && $type->allowsNull()) {
            return null;
        }

        throw new \InvalidArgumentException("Parameter '{$parameter->getName()}' is not nullable and has no default value.");
    }

    private function handleUnsetProperty(object $instance, ReflectionProperty $property): void
    {
        $type = $property->getType();

        if ($type && $type->allowsNull()) {
            $property->setValue($instance, null);
        } else {
            throw new \InvalidArgumentException("Property '{$property->getName()}' is not nullable and must be set.");
        }
    }

    private function castToType($value, string $typeName, bool $allowsNull)
    {
        if ($value === null && $allowsNull) {
            return null;
        }

        return match ($typeName) {
            'int' => (int) $value,
            'float' => (float) $value,
            'string' => (string) $value,
            'bool' => (bool) $value,
            'array' => $this->handleArrayType($value),
            default => $this->handleComplexType($value, $typeName),
        };
    }

    private function handleArrayType($value)
    {
        if (! is_array($value)) {
            return (array) $value;
        }

        $arrayOfAttribute = $this->getArrayOfAttribute();
        if ($arrayOfAttribute) {
            return array_map(fn ($item) => $this->castToType($item, $arrayOfAttribute->type, true), $value);
        }

        return $value;
    }

    private function getArrayOfAttribute(): ?ArrayOf
    {
        $attributes = $this->reflection->getAttributes(ArrayOf::class);

        return ! empty($attributes) ? $attributes[0]->newInstance() : null;
    }

    private function castToUnionType($value, ReflectionUnionType $type)
    {
        if ($value === null && $type->allowsNull()) {
            return null;
        }

        foreach ($type->getTypes() as $subType) {
            try {
                return $this->castToType($value, $subType->getName(), $subType->allowsNull());
            } catch (\Throwable $e) {
                continue;
            }
        }

        throw new \InvalidArgumentException('Unable to cast value to any of the union types.');
    }

    private function handleComplexType($value, string $typeName)
    {
        if (enum_exists($typeName)) {
            return $this->castToEnum($value, $typeName);
        }

        if (class_exists($typeName)) {
            return (new self($typeName))->fill((array) $value);
        }

        throw new \InvalidArgumentException("Unable to handle type: $typeName");
    }

    private function castToEnum($value, string $enumClass)
    {
        if (method_exists($enumClass, 'tryFrom')) {
            $enumValue = $enumClass::tryFrom($value);
            if ($enumValue !== null) {
                return $enumValue;
            }
        }

        throw new \InvalidArgumentException("Invalid enum value for $enumClass");
    }
}
