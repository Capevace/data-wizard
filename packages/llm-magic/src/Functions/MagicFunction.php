<?php

namespace Mateffy\Magic\Functions;

use Closure;
use Illuminate\Container\Container;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\Loop\EndConversation;
use Mateffy\Magic\Prompt\Role;

class MagicFunction implements InvokableFunction
{
    public function __construct(
        protected string $name,
        protected array $schema,
        protected Closure $callback,
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function schema(): array
    {
        return $this->schema;
    }

    public function validate(array $arguments): array
    {
        // TODO: Implement JSON schema validation.

        return $arguments;
    }

    public function execute(FunctionCall $call): mixed
    {
        return Container::getInstance()->call($this->callback, [
            ...$call->arguments,
            'call' => $call,
        ]);
    }
}
