<?php

namespace Capevace\MagicImport\Builder\Concerns;

use Capevace\MagicImport\LLM\Message\FunctionCall;
use Capevace\MagicImport\Prompt\Reflection\ReflectionSchema;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

trait HasTools
{
    public array $tools = [];

    public ?string $toolChoice = null;

    public function tools(...$tools): static
    {
        $this->tools = array_merge($this->tools, $this->processTools($tools));

        return $this;
    }

    protected function processTools(array $tools): array
    {
        $processedTools = [];

        foreach ($tools as $key => $tool) {
            if (is_callable($tool)) {
                $processedTools[] = $this->processFunctionTool($key, $tool);
            } elseif (is_object($tool)) {
                $processedTools[] = $this->processObjectTool($tool);
            } elseif (is_array($tool)) {
                $processedTools = array_merge($processedTools, $this->processTools($tool));
            }
        }

        return $processedTools;
    }

    protected function processFunctionTool($key, callable $tool): array
    {
        $reflection = new ReflectionFunction($tool);
        $name = is_string($key) ? $key : $reflection->getName();

        return [
            'name' => $name,
            'description' => $this->getFunctionDescription($reflection),
            'parameters' => $this->getFunctionParameters($reflection),
            'function' => function (array $arguments) use ($tool, $name) {
                $result = $tool(...$arguments);

                return new FunctionCall($name, $arguments);
            },
        ];
    }

    protected function processObjectTool(object $tool): array
    {
        $reflection = new ReflectionClass($tool);
        $method = $reflection->getMethod('__invoke');
        $name = $reflection->getShortName();

        return [
            'name' => $name,
            'description' => $this->getClassDescription($reflection),
            'parameters' => $this->getFunctionParameters($method),
            'function' => function (array $arguments) use ($tool, $name) {
                $result = $tool(...$arguments);

                return new FunctionCall($name, $arguments);
            },
        ];
    }

    protected function getFunctionDescription(ReflectionFunction $reflection): ?string
    {
        $docComment = $reflection->getDocComment();
        if ($docComment) {
            preg_match('/@description\s+(.+)/i', $docComment, $matches);

            return $matches[1] ?? null;
        }

        return null;
    }

    protected function getClassDescription(ReflectionClass $reflection): ?string
    {
        $docComment = $reflection->getDocComment();
        if ($docComment) {
            preg_match('/@description\s+(.+)/i', $docComment, $matches);

            return $matches[1] ?? null;
        }

        return null;
    }

    protected function getFunctionParameters(ReflectionFunctionAbstract $reflection): array
    {
        $parameters = [];
        foreach ($reflection->getParameters() as $param) {
            $parameters[$param->getName()] = $this->getParameterSchema($param);
        }

        return ['type' => 'object', 'properties' => $parameters];
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

    public function toolChoice(?string $name = 'auto'): static
    {
        $this->toolChoice = $name;

        return $this;
    }
}
