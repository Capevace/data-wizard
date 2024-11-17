<?php

namespace Mateffy\Magic\Builder\Concerns;

use Closure;
use Mateffy\Magic\Functions\Concerns\ToolProcessor;
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
     * @var Closure(FunctionCall): void|null $shouldInterrupt
     */
    public ?Closure $shouldInterrupt = null;

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
        $processor = app(ToolProcessor::class);
        $processedTools = [];

        foreach ($tools as $key => $tool) {
            if ($tool instanceof InvokableFunction) {
                if (is_numeric($key)) {
                    $key = $tool->name();
                }

                $processedTools[$key] = $tool;
            } else if (is_callable($tool)) {
                $processedTools[$key] = $processor->processFunctionTool($key, $tool);
            } elseif (is_array($tool)) {
                $processedTools = [
                    ...$processedTools,
                    ...$this->processTools($tool)
                ];
            }
        }

        return $processedTools;
    }

    public function interrupt(?Closure $shouldInterrupt): static
    {
        $this->shouldInterrupt = $shouldInterrupt;

        return $this;
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
