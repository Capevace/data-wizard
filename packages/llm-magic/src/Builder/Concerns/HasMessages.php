<?php

namespace Mateffy\Magic\Builder\Concerns;

use Mateffy\Magic\LLM\Message\Message;

trait HasMessages
{
    /** @var Message[] */
    public array $messages = [];

    /**
     * @param  Message[]  $messages
     */
    public function messages(array $messages): static
    {
        $this->messages = $messages;

        return $this;
    }

    public function addMessage(Message $message): static
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @param  Message[]  $messages
     */
    public function addMessages(array $messages): static
    {
        $this->messages = array_merge($this->messages, $messages);

        return $this;
    }
}
