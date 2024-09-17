<?php

namespace Capevace\MagicImport\Prompt;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\LLM\Message\Message;

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
}
