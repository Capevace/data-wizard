<?php

namespace Mateffy\Magic\Builder\Concerns;

use Closure;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\Functions\MagicFunction;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\Prompt\Reflection\ReflectionSchema;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Throwable;

trait HasTools
{
    public array $tools = [];

    public ?string $toolChoice = null;

    /**
     * @var Closure(Throwable): void|null $onToolError
     */
    public ?Closure $onToolError = null;

    /**
     * @throws ReflectionException
     */
    public function tools(...$tools): static
    {
        $this->tools = [
            ...$this->tools,
            ...$this->processTools($tools)
        ];

        return $this;
    }

    /**
     * @throws ReflectionException
     */
    protected function processTools(array $tools): array
    {
        $processedTools = [];

        foreach ($tools as $key => $tool) {
            if ($tool instanceof InvokableFunction) {
                if (is_numeric($key)) {
                    $key = $tool->name();
                }

                $processedTools[$key] = $tool;
            } else if (is_callable($tool)) {
                $processedTools[$key] = $this->processFunctionTool($key, $tool);
            } elseif (is_array($tool)) {
                $processedTools = [
                    ...$processedTools,
                    ...$this->processTools($tool)
                ];
            }
        }

        return $processedTools;
    }

    /**
     * @throws ReflectionException
     */
    protected function processFunctionTool($key, callable $tool): MagicFunction
    {
        $reflection = new ReflectionFunction($tool);
        $name = is_string($key) ? $key : $reflection->getName();

        $schema = $this->getFunctionParameters($reflection);

        if ($description = $this->getFunctionDescription($reflection)) {
            $schema['description'] = $description;
        }

        return new MagicFunction(
            name: $name,
            schema: $schema,
            callback: $tool,
        );
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

    public function onToolError(?Closure $onToolError): static
    {
        $this->onToolError = $onToolError;

        return $this;
    }
}
