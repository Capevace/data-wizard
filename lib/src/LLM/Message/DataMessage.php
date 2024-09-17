<?php

namespace Capevace\MagicImport\LLM\Message;

interface DataMessage extends Message
{
    public function data(): ?array;
}
