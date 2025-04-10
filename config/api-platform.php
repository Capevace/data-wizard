<?php

use ApiPlatform\Metadata\UrlGeneratorInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;

return [
    'title' => 'DataWizard.ai API',
    'description' => 'API access to DataWizard data extraction',
    'version' => '1.0.0',
    'show_webby' => false,

    'routes' => [
        'domain' => null,
        // Global middleware applied to every API Platform routes
         'middleware' => []
    ],

    'resources' => [
        app_path('Models'),
    ],

    'formats' => [
        'json' => ['application/json'],
        'jsonapi' => ['application/vnd.api+json'],
        'jsonld' => ['application/ld+json'],
        'csv' => ['text/csv'],
    ],

    'patch_formats' => [
        'json' => ['application/merge-patch+json'],
    ],

    'docs_formats' => [
        'json' => ['application/json'],
        'jsonapi' => ['application/vnd.api+json'],
        'jsonld' => ['application/ld+json'],
        'jsonopenapi' => ['application/vnd.openapi+json'],
        'html' => ['text/html'],
    ],

    'error_formats' => [
        'jsonproblem' => ['application/problem+json'],
    ],

    'defaults' => [
        'pagination_enabled' => true,
        'pagination_partial' => false,
        'pagination_client_enabled' => false,
        'pagination_client_items_per_page' => false,
        'pagination_client_partial' => false,
        'pagination_items_per_page' => 30,
        'pagination_maximum_items_per_page' => 30,
        'route_prefix' => '/api',
        'middleware' => [],
    ],

    'pagination' => [
        'page_parameter_name' => 'page',
        'enabled_parameter_name' => 'pagination',
        'items_per_page_parameter_name' => 'itemsPerPage',
        'partial_parameter_name' => 'partial',
    ],

    'graphql' => [
        'enabled' => true,
        'nesting_separator' => '__',
        'introspection' => ['enabled' => true],
        'max_query_complexity' => 500,
        'max_query_depth' => 200
    ],

    'exception_to_status' => [
        AuthenticationException::class => 401,
        AuthorizationException::class => 403
    ],

    'swagger_ui' => [
        'enabled' => true,
//        'apiKeys' => [
//            'api' => [
//                'type' => 'http',
//                'scheme' => 'bearer',
//                'name' => 'Bearer',
//            ]
//        ],
        //'oauth' => [
        //    'enabled' => true,
        //    'type' => 'oauth2',
        //    'flow' => 'authorizationCode',
        //    'tokenUrl' => '',
        //    'authorizationUrl' =>'',
        //    'refreshUrl' => '',
        //    'scopes' => ['scope1' => 'Description scope 1'],
        //    'pkce' => true
        //],
        //'license' => [
        //    'name' => 'Apache 2.0',
        //    'url' => 'https://www.apache.org/licenses/LICENSE-2.0.html',
        //],
        'contact' => [
            'name' => 'Lukas Mateffy',
            'url' => 'https://mateffy.me',
            'email' => 'hey@mateffy.me',
        ],
        'http_auth' => [
            'Personal Access Token' => [
                'scheme' => 'bearer',
                'bearerFormat' => 'JWT'
            ]
        ]
    ],

    // 'openapi' => [
    //     'tags' => []
    // ],

    'url_generation_strategy' => UrlGeneratorInterface::ABS_PATH,

    'serializer' => [
        'hydra_prefix' => false,
        // 'datetime_format' => \DateTimeInterface::RFC3339
    ],

    // we recommend using "file" or "acpu"
    'cache' => 'file',
];
