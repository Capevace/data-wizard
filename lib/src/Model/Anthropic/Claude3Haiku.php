<?php

namespace Capevace\MagicImport\Model\Anthropic;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Loop\Response\Streamed\ClaudeResponseDecoder;
use Capevace\MagicImport\Model\ChatLLM;
use Capevace\MagicImport\Model\ModelCost;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Prompt;
use Capevace\MagicImport\Prompt\Role;
use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use OpenAI\Laravel\Facades\OpenAI;
use \OpenAI\Responses\StreamResponse;

readonly class Claude3Haiku extends Claude3
{
    public function name(): string
    {
        return 'claude-3-haiku-20240307';
    }

    public function cost(): ?ModelCost
    {
        /**
         * Haiku
         * Input: $0.25 / MTok
         * Output: $1.25 / MTok
         */
        return new ModelCost(
            inputCentsPer1K: 0.025,
            outputCentsPer1K: 0.125
        );
    }
}
