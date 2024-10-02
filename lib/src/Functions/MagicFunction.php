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
        $output = Container::getInstance()->call($this->callback, $call->arguments);

        if ($output instanceof EndConversation) {
            return FunctionOutputMessage::end(call: $call, output: $output->getOutput());
        } else if (is_null($output)) {
            return FunctionOutputMessage::output(call: $call, output: 'null');
        } else if (is_string($output)) {
            return FunctionOutputMessage::output(call: $call, output: $output);
        } else {
            return FunctionOutputMessage::output(call: $call, output: json_encode($output, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
    }
}
