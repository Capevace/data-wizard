<?php

namespace Mateffy\Magic\LLM\Message\MultimodalMessage;

use Illuminate\Contracts\Support\Arrayable;
use Livewire\Wireable;
use Mateffy\Magic\LLM\Message\FunctionCall;
use Mateffy\Magic\LLM\Message\WireableViaArray;

readonly class ToolUse implements ContentInterface
{
    use WireableViaArray;

    public function __construct(
        public FunctionCall $call,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => 'tool_use',
            'call' => $this->call->toArray(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            call: FunctionCall::fromArray($data['call']),
        );
    }

    public static function call(FunctionCall $call): self
    {
        return new self(call: $call);
    }
}
