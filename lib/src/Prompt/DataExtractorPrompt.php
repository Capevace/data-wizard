<?php

namespace Mateffy\Magic\Prompt;

use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\Functions\SaveResources;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use JsonException;

class DataExtractorPrompt implements Prompt
{
    /**
     * @throws JsonException
     */
    public function system(): string
    {
        $schema = json_encode($this->schema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $functions = json_encode(
            array_map(fn (InvokableFunction $function) => $function->schema(), $this->functions()),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        );

        $schema = json_encode(
            $this->schema(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        );

        $functionCallSchema = json_encode(
            [
                'type' => 'object',
                'description' => 'Call one of the available functions.',
                'properties' => [
                    'function_call' => [
                        'type' => 'string',
                    ],
                    'arguments' => [
                        'type' => 'object',
                    ],
                ],
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        );

        return <<<PROMPT
        You are a data extractor that uses a given JSON schema to extract data from a document. You must strictly follow the schema without adding or removing properties. If unsure about a property, use null. Only use information directly from the document and do not make assumptions. Never summarize information, but rewrite to make it more concise. No information should be lost.
        Do not output any plaintext. Only output the structured JSON data.

        ONLY output this json object. NEVER include any other text in the output. DO NOT format the JSON object in any way. DO NOT add markdown or any other formatting.

        SCHEMA:
        {$schema}

        NOTES ON THE JSON SCHEMA:
        The schema describes real estate data. An estate can have multiple buildings, which can have multiple rentable units. We're really interested in the rentables in the context of the rest of the estate. But make sure to include every kind of data correctly: if there's info about the estate as a whole, include it there. If it's specific to a building, include it on that level. Is it only relevant for a specific rentable unit? Include it there. Each of the three can have features. A list of available features is provided below. Only include features in your output that you find information about inside the document. Features can either be true or they can specify more data. Any feature that has a data structure specified can also just be true.

        The data will be used inside commercial real estate software, so models for CRM etc. are also provided.

        Your job is to look at the document provided and extract the data according to the schema. If you can't find a piece of information, use null. If you find information that doesn't fit the schema, ignore it. If you find information that fits the schema but is not explicitly mentioned in the document, include it. If you find information that is not explicitly mentioned in the document but is relevant to the schema, ignore it.

        Again, DO NOT output JSON schema, output JSON data in the shape of the schema. I don't need any metadata or anything else. Just the data. No \$schema etc.
        PROMPT;
    }

    /**
     * @return Message[]
     *
     * @throws JsonException
     */
    public function messages(): array
    {
        return [
            new TextMessage(role: Role::System, content: $this->system()),
        ];
    }

    public function schema(): array
    {
        $json = file_get_contents(base_path('../magic-import/schema-alt.json'));

        return json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
    }

    public function functions(): array
    {
        return [
            new SaveResources,
        ];
    }
}
