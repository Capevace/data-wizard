<?php

namespace Mateffy\Magic\LLM;

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

    public function firstFunctionInvokation(): ?FunctionInvocationMessage
    {
        return $this->first(fn (Message $message) => $message instanceof FunctionInvocationMessage);
    }

    public function firstFunctionOutput(): ?FunctionOutputMessage
    {
        return $this->first(fn (Message $message) => $message instanceof FunctionOutputMessage);
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

    public function lastFunctionInvokation(): ?FunctionInvocationMessage
    {
        return $this->last(fn (Message $message) => $message instanceof FunctionInvocationMessage);
    }

    public function lastFunctionOutput(): ?FunctionOutputMessage
    {
        return $this->last(fn (Message $message) => $message instanceof FunctionOutputMessage);
    }
}
