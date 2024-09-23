<?php

namespace Mateffy\Magic\Prompt;

use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use JsonException;

class OutputFixedJson implements Prompt
{
    public function __construct(protected array|string $json) {}

    public function system(): string
    {
        return <<<'PROMPT'
        Reply with a specific JSON object that you will be given. Output only this JSON object. Do not include any other text in the output. Do not format the JSON object in any way. Do not add markdown or any other formatting.

        Minified mode: Enabled, output minified JSON.
        PROMPT;
    }

    /**
     * @return Message[]
     *
     * @throws JsonException
     */
    public function messages(): array
    {
        $json = is_string($this->json)
            ? $this->json
            : json_encode($this->json, JSON_THROW_ON_ERROR);

        return [
            new TextMessage(role: Role::System, content: $this->system()),
            new TextMessage(role: Role::User, content: "Output exactly the following JSON object: \n\n{$json}"),
        ];
    }

    public function functions(): array
    {
        return [];
    }
}
