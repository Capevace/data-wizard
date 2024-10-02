<?php

namespace Mateffy\Magic\LLM;

use Illuminate\Support\Collection;
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

    public function firstJsonMessage(): ?JsonMessage
    {
        return $this->first(fn (Message $message) => $message instanceof JsonMessage);
    }

    public function firstJson(): ?array
    {
        return $this->firstJson()?->data();
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

    public function lastJson(): ?array
    {
        return $this->last(fn (Message $message) => $message instanceof JsonMessage);
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
