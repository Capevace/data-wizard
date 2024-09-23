<?php

namespace Mateffy\Magic\Prompt;

use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\Functions\ModifyData;
use Mateffy\Magic\LLM\Message\TextMessage;

class SmartModifyPrompt implements Prompt
{
    public function __construct(
        protected string $dataToModify,
        protected array $schema,
        protected string $modificationInstructions,
        protected bool $shouldForceFunction = true,
    ) {}

    public function system(): string
    {
        return <<<'PROMPT'
        <instructions>
        You are a JSON modification assistant.
        You are given a JSON object that you need to modify according to modification instructions.
        Here's the trick: the JSON needs to conform to a given schema, so you do not have much wiggle room there. You MUST comply with the schema and not add or remove properties.

        However, you excel at moving data around, reforming it, or coming from a different perspective.
        Take a good look at the user's instructions and the JSON object, and try to make it work, without changing the schema.
        For example, you may be asked to rewrite texts, or split up some data entities that got merged accidentally (products etc).

        You can output the JSON minified, by leaving out the whitespace.
        </instructions>
        PROMPT;
    }

    public function prompt(): string
    {
        $json = json_encode($this->dataToModify, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $schema = json_encode($this->schema, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return <<<TXT
        <task>Modify the JSON object according to the instructions.</task>

        <modifications>
        {$this->modificationInstructions}
        </modifications>

        <json-schema>
        {$schema}
        </json-schema>

        <data-to-modify>
        {$json}
        </data-to-modify>
        TXT;
    }

    public function messages(): array
    {
        return [
            new TextMessage(role: Role::User, content: $this->prompt()),
        ];
    }

    public function functions(): array
    {
        return array_filter([$this->forceFunction()]);
    }

    public function forceFunction(): ?InvokableFunction
    {
        return $this->shouldForceFunction
            ? new ModifyData(schema: $this->schema)
            : null;
    }
}
