<?php

$data = \Capevace\MagicImport\Magic::extract()
    ->model('anthropic/claude-3.5-sonnet');

$data = \Capevace\MagicImport\Magic::extract()
    ->model('anthropic/claude-3.5-sonnet')
    ->system(<<<'PROMPT'
    You need to carry out data extraction from the provided document and transform it into a structured JSON format.
    PROMPT)
    ->schema([
        'type' => 'object',
        'properties' => [
            'title' => [
                'type' => 'string',
            ],
            'author' => [
                'type' => 'string',
            ],
            'excerpt' => [
                'type' => 'string',
            ],
        ],
        'required' => ['title', 'author'],
    ])
    ->stream(onDataProgress: fn () => null);

$messages = \Capevace\MagicImport\Magic::chat()
    ->model('anthropic/claude-3.5-sonnet')
    ->system(<<<'PROMPT'
    You need to carry out data extraction from the provided document and transform it into a structured JSON format.
    PROMPT)
    ->functions([
        /**
         * @param  string  $songTitle  This is the description of the parameter
         * @return \GeniusAPI\Song
         */
        'searchOnGenius' => fn (string $songTitle) => \GeniusAPI::search($songTitle)->first(),
        'searchOnGoogle' => new SearchOnGoogleFunction,
    ])
    ->messages([
        new TextMessage(role: Role::User, content: "Hello, I'm Lukas, what's your name?"),
        new TextMessage(role: Role::Assistant, content: 'I am Snoop Dogg AI-ssistant, how can I help you?'),
        new TextMessage(role: Role::User, content: "Please find a song with the title 'I'm a Slave for Love' and write it's lyrics."),
    ]);
