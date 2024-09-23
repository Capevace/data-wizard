<?php

namespace Mateffy\Magic\Prompt;

use Mateffy\Magic\Functions\ExtractData;
use Mateffy\Magic\LLM\Message\Message;
use Mateffy\Magic\LLM\Message\TextMessage;
use JsonException;

class FunctionBasedExtractorPrompt implements Prompt
{
    /**
     * @throws JsonException
     */
    public function system(): string
    {
        return <<<PROMPT
        You are a data extractor that uses a given JSON schema to extract data from a document. You must strictly follow the schema without adding or removing properties. If unsure about a property, use null. Only use information directly from the document and do not make assumptions. Never summarize information, but rewrite to make it more concise. No information should be lost.
        Do not output any plaintext. Only output the structured JSON data.

        You will supply the JSON data using the `extractData` function. You are provided with the valid parameters for this function, this is the JSON schema you NEED to follow.

        NOTES FOR DATA EXTRACTION:
        The schema describes real estate data. Buildings can have multiple spaces that are rentable. Do not just add the spaces of the same type together, include them all one by one. A list of available features is provided below. Only include features in your output that you find information about inside the document.
        The data will be used inside commercial real estate software, so models for CRM etc. are also provided.

        You have the following entities already in your memory. If you want to append data to them, just supply their ID in the rentable, then it will be added instead of created.
        Memory:
        {
            "estates": [],
            "buildings": [],
            "rentables": [
                '7f1f3837-6294-49da-9495-8844b88cbfb4' => 'Halbe Etage\n/ ca. 260 m²\n\n/ ca. 10-15 Arbeitsplätze\n/ Zellen- und offene Bürostruktur\n/ 1 Teeküche\n/ 1 WC-Kern\n/ Besprechungsräume\n/ Serverraum\n\ncia. 260 m²'
            ]
        }

        Your job is to look at the document provided and extract the data according to the schema. If you can't find a piece of information, use null. If you find information that doesn't fit the schema, ignore it. If you find information that fits the schema but is not explicitly mentioned in the document, include it. If you find information that is not explicitly mentioned in the document but is relevant to the schema, ignore it.

        When writing text, make sure to stay in the language of the user request, it may be German.
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
            new ExtractData,
        ];
    }
}
