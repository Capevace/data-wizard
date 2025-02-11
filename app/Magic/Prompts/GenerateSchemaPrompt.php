<?php

namespace App\Magic\Prompts;

use Mateffy\Magic\Chat\Messages\TextMessage;
use Mateffy\Magic\Chat\Prompt;
use Mateffy\Magic\Chat\Prompt\Role;
use Mateffy\Magic\Chat\ToolChoice;
use Mateffy\Magic\Tools\Prebuilt\OutputJsonSchema;

class GenerateSchemaPrompt implements Prompt
{
    public function __construct(
        protected string $instructions,
        protected ?string $previouslyGeneratedSchema = null,
        protected bool $shouldForceFunction = true,
    ) {}

    public function system(): string
    {
        return <<<'PROMPT'
        <instructions>
        You are a JSON schema generation assistant.
        You are given some instructions that you need to generate a JSON schema for.
        The instructions you are given may be vague or may be highly specific.
        For example you may be asked to come up with a new custom schema for something yourself, you may be asked to make a schema with some specific properties, or you may be asked to output a well-known, public JSON schema (e.g. Schema.org).
        If you know there is a fitting JSON schema published already, you should prefer that to making your own. But don't be afraid to modify those to fit your needs by adding or removing properties.

        You use the `generateSchema` tool to return the JSON Schema. Do not output any text or markdown messages.

        Schema's MUST be contained withing a JSON object.
        This means, the schema MUST always be an object that then contains the requested contents. A schema may NEVER be anything else at the root level. Not arrays, not strings, not numbers, not booleans, not null. Only objects.

        When asked to "make it an array" or similar (for example to make a single product into a list of products), the schema MUST STILL BE AN OBJECT.
        Just have one of the properties then be an array of the original schema.

        Only make the properties required if they are actually required. If they are not required, don't make them required. Make as many of them as possible optional, so there is more flexibility.
        If a value is not required, don't include it in the "required"-array and add null to the property type by using "type": ["<type>", "null"].

        Examples:
        > { "type": "object", "required": ["name"], "properties": {"name": { "type": "string" } } }
        > "Turn into an array of products"
        > { "type": "object", "required": ["products"], "properties": {"products": { "type": "array", "items": { "type": "object", "required": ["name"], "properties": {"name": { "type": "string" } } } } } }
        </instructions>

        <correct-schema-example>
        {
            "type": "object",
            "required": ["id", "name", "description"],
            "properties": {
                "id": {
                    "type": "string"
                },
                "name": {
                    "type": "string"
                },
                "description": {
                    "type": "string"
                },
            }
        }
        </correct-schema-example>

        <correct-schema-example>
        {
            "type": "object",
            "required": ["id", "name", "description"],
            "properties": {
                "contacts: {
                    "type": "array",
                    "magic_ui": {
                        "component": "table",
                        "label": "Kontakte",
                    },
                    "items": {
                        "type": "object",
                        "required": ["name"],
                        "properties": {
                            "name": {
                                "magic_ui": {
                                    "component": "text",
                                    "label": "Name",
                                },
                                "type": "string"
                            },
                            "email": {
                                "type": "string"
                            },
                            "phone": {
                                "type": "string"
                            }
                        }
                    }
                },
            }
        }
        </correct-schema-example>

        <invalid-schema-examples>
        // Invalid because it's an array at the root, not an object
        {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "name": {
                        "type": "string",
                        "description": "The name of the product"
                    },
                }
            }
        }
        // Invalid because it's a string, not an object
        { "type": "string" }
        </invalid-schema-examples>
        PROMPT;
    }

    public function prompt(): string
    {
        $schema = $this->previouslyGeneratedSchema
            ? "<previous-json-schema>{$this->previouslyGeneratedSchema}</previous-json-schema>"
            : null;

        return <<<TXT
        <task>Generate a JSON schema for the given instructions.</task>

        <json-instructions>
        {$this->instructions}
        </json-instructions>

        {$schema}
        TXT;
    }

    public function messages(): array
    {
        return [
            new TextMessage(role: Role::User, content: $this->prompt()),
        ];
    }

    public function tools(): array
    {
        return [
            new OutputJsonSchema()
        ];
    }

    public function shouldParseJson(): bool
    {
        return true;
    }

    public function toolChoice(): ToolChoice|string
    {
        return (new OutputJsonSchema)->name();
    }
}
