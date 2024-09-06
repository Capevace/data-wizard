<?php

namespace Capevace\MagicImport\Loop\Response;

use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Role;

readonly class TextResponse implements LLMResponse
{
    public function __construct(
        public string $content
    ) {}

    public function toMessage(): Message
    {
        return new TextMessage(role: Role::Assistant, content: $this->content);
    }
}
