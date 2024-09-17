<?php

namespace Capevace\MagicImport\LLM\Exceptions;

use Throwable;

class TooManyTokensForModelRequested extends InvalidRequest
{
    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('More tokens requested than the model can handle', $message, $code, $previous);
    }
}
