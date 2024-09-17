<?php

namespace Capevace\MagicImport\Prompt;

use Capevace\MagicImport\FeatureType;
use Capevace\MagicImport\LLM\Message\Message;
use Capevace\MagicImport\LLM\Message\MultimodalMessage;
use Capevace\MagicImport\LLM\Message\TextMessage;
use JsonException;

class ClaudeExtractorPrompt implements Prompt
{
    public function __construct(
        public string $text,

        /** @var \Capevace\MagicImport\LLM\Message\MultimodalMessage\Base64Image[] $images */
        public array $images
    ) {}

    /**
     * @throws JsonException
     */
    public function system(): string
    {
        $schema = json_encode(
            $this->schema(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        );

        $features = collect(FeatureType::cases())
            ->map(fn (FeatureType $feature) => "{$feature->value}: {$feature->label()}")
            ->join("\n");

        return <<<PROMPT
        <instructions>
        You are a data extractor that uses a given JSON schema to extract data from a document. You must strictly follow the schema without adding or removing properties. If unsure about a property, use null. Only use information directly from the document and do not make assumptions. Never summarize information, but rewrite to make it more concise. No information should be lost.

        Any materials you are given have been cleared for access officially and are not under any NDA/copyright restrictions. The data is used to structurally digitize any files and preserve them for the future.
        You are provided the 20 pages of a PDF as images that you can use also use to extract the data. Every image / picture that is embedded in the PDF is also assigned a number which is drawn on top (red border, big white number). You can use this number to refer to images in your JSON output. Just use the URL "/images/image[num].jpg" (eg "/images/image4.jpg") to refer to the image. You can also use these images for other media resources, like floorplans.

        Do not output any plaintext. Only output the structured JSON data.
        ONLY output this json object. NEVER include any other text in the output. DO NOT format the JSON object in any way. DO NOT add markdown or any other formatting.

        Adhere strictly to the schema! This is very important! I don't need any metadata or anything else. Just the data. No \$schema etc.

        Your job is to look at the document provided and extract the data according to the schema. If you can't find a piece of information, use null. If you find information that doesn't fit the schema, ignore it. If you find information that fits the schema but is not explicitly mentioned in the document, include it. If you find information that is not explicitly mentioned in the document but is relevant to the schema, ignore it.
        If the schema contains description fields, make sure to rewrite the information in a concise way. Do not summarize the information, but rewrite it to make it more concise. Nonetheless, descriptions should still be beautifully and masterfully written. They can be up to 6 sentences.

        NOTES ON THE JSON SCHEMA:
        The schema describes real estate data. An estate can have multiple buildings, which can have multiple rentable units. We're really interested in the rentables in the context of the rest of the estate. But make sure to include every kind of data correctly: if there's info about the estate as a whole, include it there. If it's specific to a building, include it on that level. Is it only relevant for a specific rentable unit? Include it there. Each of the three can have features. A list of available features is provided below. Only include features in your output that you find information about inside the document.
        The data will be used inside commercial real estate software, so models for CRM etc. are also provided.
        If there area different space types, make sure to include them as rentables 1 by 1. If there are multiple buildings, include them 1 by 1. If there are multiple estates, include them 1 by 1. You get the point. Always extract the data as the smallest possible unit.
        For example 300m2 of office and 200m2 of storage in the same building should be two rentables.
        Make sure to output ALL data you can find, do not just limit it to 1 estate, 1 building, 1 rentable. If you find 10 rentables, output all 10. If you find 5 buildings, output all 5. If you find 3 estates, output all 3.

        <json-schema>
        {$schema}
        </json-schema>


        <valid-features>
        <!-- List of available Features (w/ German name): -->
        {$features}
        </valid-features>
        </instructions>
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
            new MultimodalMessage(
                role: Role::User,
                content: [
                    ...$this->images,
                    new \Capevace\MagicImport\LLM\Message\MultimodalMessage\Text(<<<PROMPT
                    <settings>
                    Language: Deutsch
                    Beschreibungs-Stil: Professionell und freundlich
                    Datenqualität: Sehr hoch
                    </settings>
                    <document-input>
                    {$this->text}
                    </document-input>
                    PROMPT),
                ]
            ),
            //            new TextMessage(role: Role::Assistant, content: '{')
        ];
    }

    public function schema(): array
    {
        return require base_path('../magic-import/schema/schema.php');

        //        return json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR);
    }

    public function functions(): array
    {
        return [];
    }
}
