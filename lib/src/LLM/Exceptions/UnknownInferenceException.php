<?php

namespace Capevace\MagicImport\LLM\Exceptions;

use Throwable;

class UnknownInferenceException extends \Exception implements LLMException
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getTitle(): string
    {
        return 'An unexpected error occurred during inference.';
    }
}
