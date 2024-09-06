<?php

namespace Capevace\MagicImport\Loop\Response;

use Capevace\MagicImport\Prompt\Message\FunctionInvocationMessage;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Role;

readonly class FunctionCallResponse implements LLMResponse
{
    public function __construct(
        /**
         * @var FunctionCall
         */
        public FunctionCall $functionCall
    ) {}

    public function toMessage(): Message
    {
        return new FunctionInvocationMessage(role: Role::Assistant, call: $this->functionCall);
    }
}
