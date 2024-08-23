<?php

namespace Capevace\MagicImport\Model\Anthropic;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Loop\Response\Streamed\ClaudeResponseDecoder;
use Capevace\MagicImport\Model\ChatLLM;
use Capevace\MagicImport\Model\ModelCost;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Role;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use OpenAI\Laravel\Facades\OpenAI;
use \OpenAI\Responses\StreamResponse;

readonly class Claude3Sonnet extends Claude3
{
    public function name(): string
    {
        return 'claude-3-sonnet-20240229';
    }

    public function cost(): ?ModelCost
    {
        /**
         * Sonnet
         * Input: $3 / MTok
         * Output: $15 / MTok
         */

        return new ModelCost(
            inputCentsPer1K: 0.3,
            outputCentsPer1K: 1.5
        );
    }
}