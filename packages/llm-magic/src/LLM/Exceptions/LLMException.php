<?php

namespace Mateffy\Magic\LLM\Exceptions;

interface LLMException
{
    public function getTitle(): string;
}
