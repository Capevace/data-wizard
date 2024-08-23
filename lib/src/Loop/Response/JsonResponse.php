<?php

namespace Capevace\MagicImport\Loop\Response;

use Capevace\MagicImport\Prompt\Message\JsonMessage;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Role;

readonly class JsonResponse implements LLMResponse
{
    public function __construct(
        public array $data
    )
    {
    }

    public function toMessage(): Message
    {
        return new JsonMessage(role: Role::Assistant, data: $this->data);
    }
}