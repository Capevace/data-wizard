<?php

namespace Capevace\MagicImport\Loop\Response;

use Capevace\MagicImport\Prompt\Message\Message;

interface LLMResponse
{
    public function toMessage(): Message;
}
