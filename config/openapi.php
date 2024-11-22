<?php

return [

    'collections' => [

        'default' => [

            'info' => [
                'title' => 'Data-Wizard.ai',
                'description' => null,
                'version' => '1.0.0',
                'contact' => [],
            ],

            'servers' => [
                [
                    'url' => 'https://data-wizard.ai',
                    'description' => 'Production server',
                    'variables' => [],
                ],
                [
                    'url' => 'https://data-wizard.test',
                    'description' => 'Development server',
                    'variables' => [],
                ],
            ],

            'tags' => [

                // [
                //    'name' => 'user',
                //    'description' => 'Application users',
                // ],

            ],

            'security' => [
//                 GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement::create()
//                     ->securityScheme(\GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityScheme::TYPE_API_KEY),
            ],

            // Non standard attributes used by code/doc generation tools can be added here
            'extensions' => [
                // 'x-tagGroups' => [
                //     [
                //         'name' => 'General',
                //         'tags' => [
                //             'user',
                //         ],
                //     ],
                // ],
            ],

            // Route for exposing specification.
            // Leave uri null to disable.
            'route' => [
                'uri' => '/openapi',
                'middleware' => [],
            ],

            // Register custom middlewares for different objects.
            'middlewares' => [
                'paths' => [
                    //
                ],
                'components' => [
                    //
                ],
            ],

        ],

    ],

    // Directories to use for locating OpenAPI object definitions.
    'locations' => [
        'callbacks' => [
            app_path('OpenApi/Callbacks'),
        ],

        'request_bodies' => [
            app_path('OpenApi/RequestBodies'),
        ],

        'responses' => [
            app_path('OpenApi/Responses'),
        ],

        'schemas' => [
            app_path('OpenApi/Schemas'),
        ],

        'security_schemes' => [
            app_path('OpenApi/SecuritySchemes'),
        ],
    ],

];
