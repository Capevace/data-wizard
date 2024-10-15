<?php

namespace Mateffy\Magic\LLM\Message\MultimodalMessage;

use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\WireableViaArray;

readonly class ToolResult implements ContentInterface
{
    use WireableViaArray;

    public function __construct(
        public FunctionCall $call,
        public mixed $output = null,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => 'tool_result',
            'call' => $this->call->toArray(),
            'output' => $this->output,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            call: FunctionCall::fromArray($data['call']),
            output: $data['output'] ?? null,
        );
    }

    public static function output(FunctionCall $call, mixed $output): self
    {
        return new self(call: $call, output: $output);
    }
}
