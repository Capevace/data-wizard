<?php

namespace Capevace\MagicImport\Model\Anthropic;

readonly class Claude3Opus extends Claude3
{
    public function name(): string
    {
        return 'claude-3-opus-20240229';
    }
}
