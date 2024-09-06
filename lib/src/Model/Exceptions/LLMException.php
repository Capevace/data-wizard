<?php

namespace Capevace\MagicImport\Model\Exceptions;

interface LLMException
{
    public function getTitle(): string;
}
