<?php

namespace Capevace\MagicImport\Loop\Response\Streamed;

use Capevace\MagicImport\Loop\Response\FunctionCall;
use Capevace\MagicImport\Loop\Response\FunctionCallResponse;
use Capevace\MagicImport\Loop\Response\LLMResponse;
use Capevace\MagicImport\Prompt\Role;

class PartialFunctionCallResponse implements PartialResponse
{
    public function __construct(
        public readonly Role $role,
        public readonly string $name,
        public string $arguments = ''
    ) {}

    public function append(string $content): static
    {
        $this->arguments .= $content;

        return $this;
    }

    public function toResponse(): LLMResponse
    {
        return new FunctionCallResponse(
            new FunctionCall(
                $this->name,
                [
                    'arguments' => json_decode($this->arguments, true),
                    'arguments_raw' => $this->arguments,
                ]
            )
        );
    }

    public function role(): Role
    {
        return $this->role;
    }

    public function copy(): static
    {
        return new static($this->role, $this->name, $this->arguments);
    }
}
