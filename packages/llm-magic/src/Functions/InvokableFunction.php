<?php

namespace Mateffy\Magic\Functions;

use Closure;
use Mateffy\Magic\LLM\Message\FunctionCall;

interface InvokableFunction
{
    public function name(): string;

    public function schema(): array;

    public function validate(array $arguments): array;

    public function execute(FunctionCall $call): mixed;
}
