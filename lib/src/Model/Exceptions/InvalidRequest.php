<?php

namespace Capevace\MagicImport\Model\Exceptions;

use Throwable;

class InvalidRequest extends \Exception implements LLMException
{
    public function __construct(protected string $title, string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}