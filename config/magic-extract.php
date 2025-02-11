<?php

return [
    'default-model' => env('MAGIC_DEFAULT_MODEL', 'openai/gpt-4o-mini'),
    'uv' => [
        'path' => env('MAGIC_EXTRACT_UV_PATH', '/usr/bin/env uv'),
    ],

    'apis' => [
        'anthropic' => [
            'token' => env('ANTHROPIC_API_KEY'),
        ],
        'openai' => [
            'token' => env('OPENAI_API_KEY'),
            'organization_id' => env('OPENAI_ORGANIZATION_ID'),
        ],
        'groq' => [
            'token' => env('GROQ_API_KEY'),
        ],
        'gemini' => [
            'token' => env('GEMINI_API_KEY'),
        ],
        'openrouter' => [
            'token' => env('OPENROUTER_API_KEY'),
            'url' => env('OPENROUTER_API_URL', 'openrouter.ai/api/v1'),
        ],
        'togetherai' => [
            'token' => env('TOGETHERAI_API_KEY'),
            'url' => env('TOGETHERAI_API_URL', 'api.together.xyz/v1'),
        ]
    ],

    'extractors' => [
        'crm' => [
            'title' => 'Kontakt/Firma importieren',
            'description' => 'Extrahiere Daten aus verschiedenen Dokumenten und importiere sie in dein CRM-System.',

            'files' => [
                'maxSize' => 50, // MB
                'maxNumberOfFiles' => null, // Unlimited
                'allowedTypes' => [
                    // Using MIME types
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/gif',

                    // Using presets
                    'documents',
                    'images',

                    // Using specific type/preset options
                    ['type' => 'application/pdf', 'extractImages' => false], // Don't extract images from PDFs
                    ['type' => 'images', 'maxSize' => 20], // Allow images up to 20 MB
                ],
            ],

            'llm' => [
                'model' => 'openai/gpt-4',
                'options' => [
                    'temperature' => 0.5,
                    'maxTokens' => 100,
                    'topP' => 0.9,
                ],
            ],

            'strategy' => 'simple', // "sequential", "parallel"

            'output' => [
                'format' => [ // JSON Schema
                    'contacts' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => 'string'],
                                'email' => ['type' => 'string', 'format' => 'email'],
                                'phone' => ['type' => 'string'],
                            ],
                            'required' => ['name'],
                        ],
                    ],
                    'companies' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => 'string'],
                                'website' => ['type' => 'string', 'format' => 'uri'],
                                'phone' => ['type' => 'string', 'format' => 'phone'],
                            ],
                            'required' => ['name'],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
