<?php

namespace Mateffy\Magic\Prompt;

use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\Message;

interface Prompt
{
    public function system(): ?string;

    /**
     * @return Message[]
     */
    public function messages(): array;

    /**
     * @return InvokableFunction[]
     */
    public function functions(): array;

    /**
     * Wether to parse the output as JSON.
     * @return bool
     */
    public function shouldParseJson(): bool;
}
