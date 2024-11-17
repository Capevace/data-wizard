<?php

namespace Mateffy\Magic\Functions\Exceptions;

use Throwable;

class ToolCallException extends \RuntimeException
{
    protected string $error_code;

    public function __construct(
        string $message = "",
        string $code = '',
        ?Throwable $previous = null
    )
    {
        parent::__construct($message, 0, $previous);

        $this->error_code = $code;
    }

    public function toLLMString(): string
    {
        return json_encode([
            'tool_success' => false,
            'error' => $this->getMessage(),
            'error_code' => $this->error_code
        ]);
    }
}
