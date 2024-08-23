<?php

namespace Capevace\MagicImport\Model\Anthropic;

use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Loop\Response\Streamed\ClaudeResponseDecoder;
use Capevace\MagicImport\Model\ChatLLM;
use Capevace\MagicImport\Prompt\Message\Message;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Capevace\MagicImport\Prompt\Role;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use OpenAI\Laravel\Facades\OpenAI;
use \OpenAI\Responses\StreamResponse;

readonly class Claude3Opus extends Claude3
{
    public function name(): string
    {
        return 'claude-3-opus-20240229';
    }
}