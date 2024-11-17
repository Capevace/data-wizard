<?php

namespace Mateffy\Magic\Buckets\Functions;

use Mateffy\Magic\Functions\Concerns\ToolProcessor;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\FunctionCall;

trait AutoprocessInvokable
{
    public function name(): string
    {
        return static::$name;
    }

    public function build(): InvokableFunction
    {
        $processor = app(ToolProcessor::class);

        return $processor->processFunctionTool($this->name(), \Closure::fromCallable([$this, '__invoke']));
    }

    public function schema(): array
    {
        return $this->build()->schema();
    }

    public function validate(array $arguments): array
    {
        return $this->build()->validate($arguments);
    }

    public function execute(FunctionCall $call): mixed
    {
        return $this->build()->execute($call);
    }
}
