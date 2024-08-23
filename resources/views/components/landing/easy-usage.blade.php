<?php
$js = <<<JS
import MagicExtract from '@magic-extract/sdk';
import { createExtractor, LocalArtifact, SimpleStrategy, configure } from '@magic-extract/sdk';

// Optionally configure the Server globally
MagicExtract.configure({
    url: 'https://magic-extract.fly.dev',
    apiKey: 'your-api-key',
});

const extractor = MagicExtract.extractor({
    server: { url: 'https://magic-extract.fly.dev', apiKey: 'your-api-key' },
    llm: 'anthropic/claude-sonnet-3.5',
    description: 'Extract the title, author, and excerpt from book recommendations',
    schema: {
        type: 'object',
        properties: {
            title: {
                type: 'string',
            },
            author: {
                type: 'string',
            },
            excerpt: {
                type: 'string',
            },
        },
        required: ['title', 'author'],
    },
    strategy: 'simple'
});

const artifacts = [
    Artifact.fromPath('/path/to/file.pdf'),
];

extractor.on('message-progress', ({ message }) => {/* Latest LLM response as it is streamed */});
extractor.on('message', ({ message }) => {/* Latest LLM response once finished */});
extractor.on('data-progress', ({ data }) => {/* Partial, unfinished JSON data as it is created */});
extractor.on('token-stats', ({ stats }) => {/* Token usage & cost stats */});

const data = await extractor.run(artifacts);
JS;

$php = <<<PHP
<?php
use MagicExtract\Artifacts\LocalArtifact;
use MagicExtract\Extractor;
use MagicExtract\Strategies\SimpleStrategy;

\$extractor = new Extractor(
    llm: 'anthropic/claude-sonnet-3.5',
    description: 'Extract the title, author, and excerpt from book recommendations',
    schema: [
        'type' => 'object',
        'properties' => [
            'title' => [
                'type' => 'string',
            ],
            'author' => [
                'type' => 'string',
            ],
            'text' => [
                'type' => 'string',
            ],
        ],
        'required' => ['title', 'author'],
    ],
    strategy: new SimpleStrategy
);

\$extractor->run([
    Artifact::fromPath('/path/to/file.pdf'),
]);
PHP;
?>

<div {{ $attributes->class('space-y-32 pt-20 mx-auto w-full max-w-screen-lg px-5') }}>
    <h2>Usage via CLI</h2>
    <div class="text-xs grid grid-cols-2 gap-5 [&>pre]:rounded-lg [&>pre]:overflow-x-auto">
        <div>
            <h2>PHP SDK (code performing the actual extraction, used by the server)</h2>
            <x-content.code language="php">{!! $php !!}</x-content.code>
        </div>
        <div>
            <h2>JavaScript SDK (calls the HTTP API of the server)</h2>
            <x-content.code language="js">{!! $js !!}</x-content.code>
        </div>
    </div>
</div>
