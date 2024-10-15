<?php

namespace Mateffy\Magic\LLM;

use Closure;
use Illuminate\Support\Collection;
use Mateffy\Magic\LLM\Message\DataMessage;
use Mateffy\Magic\LLM\Message\FunctionInvocationMessage;
use Mateffy\Magic\LLM\Message\FunctionOutputMessage;
use Mateffy\Magic\LLM\Message\JsonMessage;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;

class MessageCollection extends Collection
{
    public function firstTextMessage(): ?TextMessage
    {
        return $this->first(fn (Message $message) => $message instanceof TextMessage);
    }

    public function firstText(): ?string
    {
        return $this->firstTextMessage()?->text();
    }

    public function firstDataMessage(): ?DataMessage
    {
        return $this->first(fn (Message $message) => $message instanceof DataMessage);
    }

    public function firstData(): ?array
    {
        return $this->firstDataMessage()?->data();
    }

    public function firstFunctionInvokation(?Closure $filter = null): ?FunctionInvocationMessage
    {
        return $this->first(fn (Message $message) => $message instanceof FunctionInvocationMessage && ($filter === null || $filter($message)));
    }

    public function firstFunctionOutput(?Closure $filter = null): ?FunctionOutputMessage
    {
        return $this->first(fn (Message $message) => $message instanceof FunctionOutputMessage && ($filter === null || $filter($message)));
    }

    public function lastText(): ?string
    {
        return $this->last(fn (Message $message) => $message instanceof TextMessage);
    }

    public function lastDataMessage(): ?DataMessage
    {
        return $this->last(fn (Message $message) => $message instanceof DataMessage);
    }

    public function lastData(): ?array
    {
        return $this->lastDataMessage()?->data();
    }

    public function lastFunctionInvokation(?Closure $filter = null): ?FunctionInvocationMessage
    {
        return $this->last(fn (Message $message) => $message instanceof FunctionInvocationMessage && ($filter === null || $filter($message)));
    }

    public function lastFunctionOutput(?Closure $filter = null): ?FunctionOutputMessage
    {
        return $this->last(fn (Message $message) => $message instanceof FunctionOutputMessage && ($filter === null || $filter($message)));
    }
}
