<?php

namespace Mateffy\Magic\Loop;

use Mateffy\Magic\LLM\Message\Message;
use Carbon\CarbonImmutable;

readonly class LoopStep
{
    public function __construct(
        /** @var Message[] */
        public array $messages,

        public bool $initiatedByUser,

        public CarbonImmutable $timestamp,
    ) {}
}
