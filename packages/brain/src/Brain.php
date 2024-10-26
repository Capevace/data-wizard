<?php

use Mateffy\Magic\Builder\ChatPreconfiguredModelBuilder;
use Mateffy\Magic\Magic;

class Brain
{
    public function strategyTick(): void
    {

    }

    public function reactTo($event): void
    {

    }

    public function magic(): ChatPreconfiguredModelBuilder
    {
        return Magic::chat()
            ->model('openai/gpt-4')
            ->system('You are a helpful chatbot. You answer questions about extracting data from files.');
    }
}
