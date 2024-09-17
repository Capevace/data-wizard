<?php

namespace Capevace\MagicImport\Prompt\Reflection;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

class ReflectionSchema
{
    protected ?ReflectionClass $reflection;

    public function __construct(protected ?string $classpath)
    {
        $this->reflection = $classpath ? new ReflectionClass($this->classpath) : null;
    }

    public function toJsonSchema(): array
    {
        if (! $this->reflection) {
            return ['type' => 'object', 'properties' => []];
        }

        $properties = [];
        $required = [];

        foreach ($this->reflection->getProperties() as $property) {
            $properties[$property->getName()] = $this->getPropertySchema($property);
            if (! $property->getType()?->allowsNull()) {
                $required[] = $property->getName();
            }
        }

        $schema = [
            'type' => 'object',
            'properties' => $properties,
        ];

        if (! empty($required)) {
            $schema['required'] = $required;
        }

        if ($description = $this->getClassDescription()) {
            $schema['description'] = $description;
        }

        return $schema;
    }

    private function getPropertySchema(ReflectionProperty $property): array
    {
        $schema = [];

        if ($description = $this->getPropertyDescription($property)) {
            $schema['description'] = $description;
        }

        $type = $property->getType();
        if ($type instanceof ReflectionNamedType) {
            $this->handleNamedType($type, $schema);
        } elseif ($type instanceof ReflectionUnionType) {
            $this->handleUnionType($type, $schema);
        }

        foreach ($property->getAttributes() as $attribute) {
            $this->applyAttributeToSchema($attribute, $schema);
        }

        return $schema;
    }

    private function handleNamedType(ReflectionNamedType $type, array &$schema): void
    {
        $typeName = $type->getName();
        if (enum_exists($typeName)) {
            $enumValues = $this->getEnumValues($typeName);
            $schema['enum'] = $enumValues;
            $schema['type'] = $this->determineEnumType($enumValues);
        } elseif (class_exists($typeName)) {
            $nestedSchema = new self($typeName);
            $schema = array_merge($schema, $nestedSchema->toJsonSchema());
        } else {
            $schema = $this->getTypeSchema($typeName);
        }
    }

    private function handleUnionType(ReflectionUnionType $type, array &$schema): void
    {
        $types = [];
        foreach ($type->getTypes() as $t) {
            if ($t instanceof ReflectionNamedType) {
                $typeName = $t->getName();
                if (class_exists($typeName)) {
                    $types[] = (new self($typeName))->toJsonSchema();
                } else {
                    $types[] = $this->getTypeSchema($typeName);
                }
            }
        }
        $schema['anyOf'] = $types;
    }

    private function getTypeSchema(string $typeName): array
    {
        $schema = ['type' => $this->mapPhpTypeToJsonSchemaType($typeName)];
        if ($typeName === 'int') {
            $schema['type'] = 'integer';
        } elseif ($typeName === 'float') {
            $schema['type'] = 'number';
        }
        return $schema;
    }

    private function getEnumValues(string $enumClass): array
    {
        return array_map(fn ($case) => $case->value, $enumClass::cases());
    }

    private function determineEnumType(array $values): string
    {
        $types = array_unique(array_map('gettype', $values));

        return count($types) === 1
            ? $this->mapPhpTypeToJsonSchemaType($types[0])
            : 'mixed';
    }

    private function getClassDescription(): ?string
    {
        $attributes = $this->reflection->getAttributes(Description::class);

        return ! empty($attributes) ? $attributes[0]->newInstance()->value : null;
    }

    private function getPropertyDescription(ReflectionProperty $property): ?string
    {
        $attributes = $property->getAttributes(Description::class);

        return ! empty($attributes) ? $attributes[0]->newInstance()->value : null;
    }

    private function mapPhpTypeToJsonSchemaType(string $phpType): string
    {
        return match ($phpType) {
            'int' => 'integer',
            'float' => 'number',
            'string' => 'string',
            'bool' => 'boolean',
            'array' => 'array',
            'object', 'stdClass' => 'object',
            default => 'object',
        };
    }

    private function applyAttributeToSchema(ReflectionAttribute $attribute, array &$schema): void
    {
        $instance = $attribute->newInstance();

        match (get_class($instance)) {
            Min::class => $this->applyMinToSchema($instance, $schema),
            Max::class => $this->applyMaxToSchema($instance, $schema),
            ArrayOf::class => $this->applyArrayOfToSchema($instance, $schema),
            Schema::class => $this->applySchemaToSchema($instance, $schema),
            Description::class => $schema['description'] = $instance->value,
            default => null,
        };
    }

    private function applyMinToSchema(Min $min, array &$schema): void
    {
        if (isset($schema['anyOf'])) {
            foreach ($schema['anyOf'] as &$type) {
                $type['minimum'] = $min->value;
            }
        } else {
            $schema['minimum'] = $min->value;
        }
    }

    private function applyMaxToSchema(Max $max, array &$schema): void
    {
        if (isset($schema['anyOf'])) {
            foreach ($schema['anyOf'] as &$type) {
                $type['maximum'] = $max->value;
            }
        } else {
            $schema['maximum'] = $max->value;
        }
    }

    private function applyArrayOfToSchema(ArrayOf $arrayOf, array &$schema): void
    {
        $schema['type'] = 'array';
        if (is_string($arrayOf->type) && class_exists($arrayOf->type)) {
            $schema['items'] = (new self($arrayOf->type))->toJsonSchema();
        } elseif (is_string($arrayOf->type)) {
            $schema['items'] = ['type' => $this->mapPhpTypeToJsonSchemaType($arrayOf->type)];
        }
    }

    private function applySchemaToSchema(Schema $schemaAttribute, array &$schema): void
    {
        if (is_string($schemaAttribute->schemaOrClasspath) && class_exists($schemaAttribute->schemaOrClasspath)) {
            $schema = array_merge($schema, (new self($schemaAttribute->schemaOrClasspath))->toJsonSchema());
        } elseif (is_array($schemaAttribute->schemaOrClasspath)) {
            $schema = array_merge($schema, $schemaAttribute->schemaOrClasspath);
        }
    }
}
