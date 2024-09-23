<?php

namespace Mateffy\Magic\LLM\Message;

interface DataMessage extends Message
{
    public function data(): ?array;
}
