<?php

namespace Capevace\MagicImport\Model;


use Capevace\MagicImport\Loop\Response\Streamed\ClaudeResponseDecoder;
use Capevace\MagicImport\Loop\Response\Streamed\Decoder;
use Capevace\MagicImport\Loop\Response\Streamed\ResponseDecoder;
use Capevace\MagicImport\Prompt\Prompt;
use Closure;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\StreamResponse;
use Psr\Http\Message\StreamInterface;

interface LLM
{
    public function name(): string;
    public function parameters(): array;

    public function stream(Prompt $prompt, ?Closure $onMessageProgress = null, ?Closure $onMessage = null): array;
    public function send(Prompt $prompt): array;

    public function cost(): ?ModelCost;
}