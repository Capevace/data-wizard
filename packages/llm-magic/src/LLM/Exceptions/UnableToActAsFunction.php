<?php

namespace Mateffy\Magic\LLM\Exceptions;

class UnableToActAsFunction extends \Exception implements LLMException
{
    public function __construct(string $error)
    {
        parent::__construct($error);
    }

    public function getTitle(): string
    {
        return 'Unable to act as function';
    }
}
