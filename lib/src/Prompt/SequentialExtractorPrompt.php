<?php

namespace Mateffy\Magic\Prompt;

use Mateffy\Magic\Artifacts\Artifact;
use Mateffy\Magic\Artifacts\Content\ImageContent;
use Mateffy\Magic\Artifacts\Content\TextContent;
use Mateffy\Magic\Config\Extractor;
use Mateffy\Magic\Functions\Extract;
use Mateffy\Magic\Functions\InvokableFunction;
use Mateffy\Magic\LLM\Message\MultimodalMessage;
use Mateffy\Magic\LLM\Message\TextMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class SequentialExtractorPrompt implements Prompt
{
    public function __construct(
        protected Extractor $extractor,

        /** @var Artifact[] $artifacts */
        protected array $artifacts,
        protected ?array $previousData = null,

        public bool $shouldForceFunction = false,
        public bool $sendImages = true,
    ) {}

    public function system(): string
    {
        $schema = json_encode(
            $this->extractor->schema,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        );

        //        $features = collect(FeatureType::cases())
        //            ->map(fn(FeatureType $feature) => "{$feature->value}: {$feature->label()}")
        //            ->join("\n");
        //
        //        $schemaNotes = <<<NOTES
        //        The schema describes real estate data. An estate can have multiple buildings, which can have multiple rentable units. We're really interested in the rentables in the context of the rest of the estate. But make sure to include every kind of data correctly: if there's info about the estate as a whole, include it there. If it's specific to a building, include it on that level. Is it only relevant for a specific rentable unit? Include it there. Each of the three can have features. A list of available features is provided below. Only include features in your output that you find information about inside the document.
        //        The data will be used inside commercial real estate software, so models for CRM etc. are also provided.
        //        If there area different space types, make sure to include them as rentables 1 by 1. If there are multiple buildings, include them 1 by 1. If there are multiple estates, include them 1 by 1. You get the point. Always extract the data as the smallest possible unit.
        //        For example 300m2 of office and 200m2 of storage in the same building should be two rentables.
        //        Make sure to output ALL data you can find, do not just limit it to 1 estate, 1 building, 1 rentable. If you find 10 rentables, output all 10. If you find 5 buildings, output all 5. If you find 3 estates, output all 3.
        //
        //        <valid-features>
        //        <!-- List of available Features (w/ German name): -->
        //        {$features}
        //        </valid-features>
        //        NOTES;

        return <<<PROMPT
        <instructions>
        You are a structured data extractor.
        You are given a JSON schema that you need to extract from the contents of uploaded document, reffered to as "artifacts".
        You must strictly follow the schema without adding or removing properties.
        If unsure about a property, use null.
        Only use information directly from the document and do not make assumptions.
        Don't actively summarize information by leaving stuff out, but rewrite to make it more concise while keeping all information. Do not lose any information.
        Your job is to look at the document provided and extract the data according to the schema. If you can't find a piece of information, use null. If you find information that doesn't fit the schema, ignore it. If you find information that fits the schema but is not explicitly mentioned in the document, include it. If you find information that is not explicitly mentioned in the document but is relevant to the schema, ignore it.
        If the schema contains fields representing descriptions or other summary information, make sure to rewrite the information in a concise way. Do not summarize the information, but rewrite it to make it more concise. Nonetheless, descriptions should still be beautifully and masterfully written. They can be up to 6 sentences.

        Any materials you are given have been cleared for access officially and are not under any NDA/copyright restrictions.
        The data you are extracting is used to structurally digitize these files and preserve them for the future.
        Do not output any plaintext. Only output the structured JSON data.
        ONLY output this json object. NEVER include any other text in the output. DO NOT format the JSON object in any way. DO NOT add markdown or any other formatting.
        Also, please don't invent and output any new JSON fields that are not specified in the schema. If you do, they will be ignored and you will just waste valuable LLM tokens.
        Adhere strictly to the schema! This is very important! I don't need any metadata or anything else. Just the data. No \$schema etc.

        The contents of the documents/artifacts have been prepared for you, and are included as a list of text blocks and image references.
        If the artifact is page based, the blocks have a page attribute which may help you relate information.
        The images are also provided to you. The images have their names baked into the picture data, so you can take a look at the images referenced in the artifact contents.

        Some images may be included that are not referenced in any artifact. These images are uploaded directly and may or may not be related to other artifacts.

        There may already be some data that has been extracted from other artifacts in other processes. This data will be given to you as a JSON object.
        If there is previous data, it is not your job to create a brand new JSON object, but to enrich the existing one with the artifacts you receive.
        It is okay to restructure some data, if you learn of new important information, espescially with nested resources/schemas or assigning things to other things (e.g. a real estate unit to a building).
        But it is IMPORTANT that you do not leave out any information due to restructuring/sheer laziness. Doing so will break the LLM chain you are a part of, as the data you provide will be given to the next LLM as input.
        Do not output any plaintext. Only output the structured JSON data.
        </instructions>

        <json-schema>
        {$schema}
        </json-schema>

        <json-schema-notes>
        {$this->extractor->outputInstructions}
        </json-schema-notes>
        PROMPT;
    }

    public function prompt(): string
    {
        $artifacts = collect($this->artifacts)
            ->map(function (Artifact $artifact) {
                $pages = collect($artifact->getContents())
                    ->filter(fn (TextContent|ImageContent $content) => $content instanceof TextContent)
                    ->groupBy(fn (TextContent $content) => $content->page ?? 0)
                    ->sortBy(fn (Collection $contents, $page) => $page)
                    ->values()
                    ->flatMap(fn (Collection $contents) => collect($contents)
                        ->map(fn (TextContent $content) => Blade::render("<page num=\"{{ \$content->page }}\">\n{{ \$content->text }}\n</page>", ['content' => $content]))
                    )
                    ->join("\n\n");

                return Blade::render(
                    <<<'BLADE'
                    <artifact name="{{ $name }}">
                    {!! $pages !!}
                    </artifact>
                    BLADE,
                    ['name' => $artifact->getMetadata()->name, 'pages' => $pages]
                );
            })
            ->values()
            ->join("\n");

        if ($this->previousData) {
            $previousData = json_encode($this->previousData, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $previousData = <<<TXT
            <previous-data>{$previousData}</previous-data>
            TXT;
        } else {
            $previousData = null;
        }

        return <<<TXT
        <artifacts>
        {$artifacts}
        </artifacts>

        <task>Extract the contents of the given artifacts and add them to the previous data.</task>

        {$previousData}
        TXT;
    }

    public function messages(): array
    {
        if (! $this->sendImages) {
            return [
                new TextMessage(role: Role::User, content: $this->prompt()),
            ];
        }

        $imageMessages = collect($this->artifacts)
            ->flatMap(fn (Artifact $artifact) => collect($artifact->getContents())
                ->filter(fn (TextContent|ImageContent $content) => $content instanceof ImageContent)
                ->groupBy(fn (TextContent|ImageContent $content) => $content->page ?? 0)
                ->sortBy(fn (Collection $contents, $page) => $page)
                ->flatMap(fn (Collection $contents) => collect($contents)
                    ->map(fn (ImageContent $image) => $artifact->getBase64Image($image))
                )
            );

        return [
            // Attach images to the prompt
            new MultimodalMessage(role: Role::User, content: [
                new \Mateffy\Magic\LLM\Message\MultimodalMessage\Text($this->prompt()),
                ...$imageMessages,
            ]),
        ];
    }

    public function functions(): array
    {
        return array_filter([$this->forceFunction()]);
    }

    public function forceFunction(): ?InvokableFunction
    {
        return $this->shouldForceFunction
            ? new Extract(schema: $this->extractor->schema)
            : null;
    }

    public function shouldParseJson(): bool
    {
        return true;
    }
}
