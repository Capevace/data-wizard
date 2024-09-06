<?php

namespace Capevace\MagicImport\Loop;

use Capevace\MagicImport\Prompt\Message\Message;
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
