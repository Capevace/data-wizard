<?php

namespace Capevace\MagicImport\Prompt;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Prompt\Message\Message;

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
     * @param  Message[]  $messages
     */
    public function withMessages(array $messages): static;
}
