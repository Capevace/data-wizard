<?php

namespace Capevace\MagicImport\LLM\Exceptions;

interface LLMException
{
    public function getTitle(): string;
}
