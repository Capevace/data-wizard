<?php

namespace Capevace\MagicImport\Prompt;

use Capevace\MagicImport\Artifacts\Artifact;
use Capevace\MagicImport\Artifacts\Content\ImageContent;
use Capevace\MagicImport\Artifacts\Content\TextContent;
use Capevace\MagicImport\Config\Extractor;
use Capevace\MagicImport\FeatureType;
use Capevace\MagicImport\Functions\Extract;
use Capevace\MagicImport\Functions\ExtractData;
use Capevace\MagicImport\Functions\InvokableFunction;
use Capevace\MagicImport\Functions\ModifyData;
use Capevace\MagicImport\Prompt\Message\MultimodalMessage;
use Capevace\MagicImport\Prompt\Message\TextMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Swaggest\JsonSchema\JsonSchema;

class SmartModifyPrompt implements Prompt
{
    public function __construct(
        protected string $dataToModify,
        protected array $schema,
        protected string $modificationInstructions,
        protected bool $shouldForceFunction = true,
    )
    {
    }

    public function system(): string
    {
        return <<<PROMPT
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

    public function withMessages(array $messages): static
    {
        return $this;
    }
}
