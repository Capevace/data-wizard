<?php

namespace Mateffy\Magic\Loop;

/**
 * Provides a way to end an LLM conversation early after a function call,
 * without doing another LLM call with the function output.
 */
class EndConversation extends \Error
{
    public function __construct(
        protected mixed $output,
    )
    {
        parent::__construct('EndConversation');
    }

    public function getOutput(): mixed
    {
        return $this->output;
    }
}
