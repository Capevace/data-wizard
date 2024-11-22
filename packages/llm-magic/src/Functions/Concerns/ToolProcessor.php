<?php

namespace Mateffy\Magic\Functions\Concerns;

use Illuminate\Support\Str;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\Functions\MagicFunction;
use Mateffy\Magic\Prompt\Reflection\ReflectionSchema;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class ToolProcessor
{

    /**
     * @throws ReflectionException
     */
    public function processFunctionTool($key, callable $tool): MagicFunction
    {
        $reflection = new ReflectionFunction($tool);
        $name = is_string($key) ? $key : $reflection->getName();

        $schema = $this->getFunctionParameters($reflection);

        if ($description = $this->getDocblockDescription($reflection)) {
            $schema['description'] = $description;
        }

        return new MagicFunction(
            name: $name,
            schema: $schema,
            callback: $tool,
        );
    }

    protected function getFunctionParameters(ReflectionFunctionAbstract $reflection): ?array
    {
        $schema = ['type' => 'object'];
        $required = [];
        $parameters = [];

        foreach ($reflection->getParameters() as $param) {
            if (Str::contains($param->getType()->getName(), 'FunctionCall')) {
                continue;
            }

            if (!$param->isOptional()) {
                $required[] = $param->getName();
            }

            if ($customType = $this->getBetterDocblockType($reflection, $param->getName())) {
                if ($description = $this->getDocblockDescription($reflection, name: $param->getName())) {
                    $customType['description'] = $description;
                }

                $parameters[$param->getName()] = $customType;
            } else {
                $parameters[$param->getName()] = $this->getParameterSchema($param);
            }
        }

        if ($description = $this->getDocblockDescription($reflection)) {
            $schema['description'] = $description;
        }

        if (count($parameters) > 0) {
            $schema['properties'] = $parameters;
        }

        if (count($required) > 0) {
            $schema['required'] = $required;
        }

        return $schema;
    }

    private function getParameterSchema(ReflectionParameter $param): array
    {
        $type = $param->getType();

        if ($type instanceof ReflectionNamedType) {
            return $this->handleNamedType($type);
        } elseif ($type instanceof ReflectionUnionType) {
            return $this->handleUnionType($type);
        } else {
            return ['type' => 'mixed'];
        }
    }

    private function handleNamedType(ReflectionNamedType $type): array
    {
        $typeName = $type->getName();
        if (class_exists($typeName)) {
            return (new ReflectionSchema($typeName))->toJsonSchema();
        } else {
            return $this->getTypeSchema($typeName);
        }
    }

    private function getTypeSchema(string $typeName): array
    {
        $schema = ['type' => $this->mapPhpTypeToJsonSchemaType($typeName)];
        if ($typeName === 'int') {
            $schema['type'] = 'integer';
        } elseif ($typeName === 'float') {
            $schema['type'] = 'number';
        } elseif ($typeName === 'array') {
            $schema['items'] = ['type' => 'string'];
        }

        return $schema;
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
            default => 'mixed',
        };
    }

    private function handleUnionType(ReflectionUnionType $type): array
    {
        $types = [];
        foreach ($type->getTypes() as $t) {
            if ($t instanceof ReflectionNamedType) {
                $types[] = $this->handleNamedType($t);
            }
        }

        return ['anyOf' => $types];
    }

    protected function getDocblockDescription(ReflectionFunctionAbstract|ReflectionFunction $reflection, ?string $name = null): ?string
    {
        $docComment = $reflection->getDocComment();

        if ($docComment) {
            if ($name) {
                preg_match("/@description\s+\\\${$name}\s+(.+)\$/mi", $docComment, $matches);
            } else {
                preg_match("/@description\s+(.+)\$/i", $docComment, $matches);
            }
            preg_match('/@description\s+(.+)/i', $docComment, $matches);

            return $matches[1] ?? null;
        }

        return null;
    }

    protected function getBetterDocblockType(ReflectionFunctionAbstract|ReflectionFunction $reflection, ?string $name = null): ?array
    {
        $docComment = $reflection->getDocComment();

        // Match @type $name {"type": "object", "properties": {"name": {"type": "string"}}}
        if ($docComment) {
            if ($name) {
                preg_match("/@type\s+\\\${$name}\s+([\\\{]?.*)\$/mi", $docComment, $matches);
            } else {
                preg_match("/@type\s+([\\\{]?.*)\$/mi", $docComment, $matches);
            }

            return json_decode(trim($matches[1] ?? null), true);
        }

        return null;
    }
}
