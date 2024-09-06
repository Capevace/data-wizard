<?php

namespace Capevace\MagicImport\Functions;

use Capevace\MagicImport\FeatureType;

class ExtractData implements InvokableFunction
{
    public function name(): string
    {
        return 'extractData';
    }

    protected function featuresSchema(): array
    {
        return [
            'description' => 'The features of the rental space',
            'type' => 'array',
            'items' => [
                'type' => 'string',
                // Enum:
                // - 'balcony'
                // - 'terrace'
                // - ...
                'enum' => collect(FeatureType::cases())
                    ->map(fn (FeatureType $featureType) => $featureType->value)
                    ->all(),
            ],
        ];
    }

    protected function rentableSchema(): array
    {
        return [
            'type' => 'object',
            'description' => 'A singular rentable / physical space. Smallest possible unit.',
            'required' => ['name', 'description', 'type', 'area', 'features'],
            'properties' => [
                'id' => [
                    'type' => 'string',
                    'description' => 'The unique identifier for this rentable unit, if you think this rentable unit is already in your memory. Otherwise, leave it empty.',
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'A name / identifier of the rental space',
                ],
                'description' => [
                    'type' => 'string',
                    'description' => 'A detailed real estate description of the rental space',
                ],
                'type' => [
                    'type' => 'string',
                    'description' => 'The type of the space',
                    'enum' => [
                        'office',
                        'retail',
                        'storage',
                        'living',
                        'other',
                    ],
                ],
                'area' => [
                    'type' => 'number',
                    'description' => 'The area of the space',
                ],
                'floor' => [
                    'type' => 'number',
                    'description' => 'The floor (0 = EG, -1 = 1. UG, 1 = 1. OG, ...)',
                ],
                'rent_per_m2' => [
                    'type' => 'number',
                    'description' => 'The rent per square meter of the space',
                ],
                'rent_total' => [
                    'type' => 'number',
                    'description' => 'The total rent of the space',
                ],
                'images' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                    'description' => 'The image urls of the rental space',
                ],
                'floorplans' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                    'description' => 'The floorplan urls of the rental space',
                ],
                'features' => $this->featuresSchema(),
            ],
        ];
    }

    protected function estateSchema()
    {
        return [
            'type' => 'object',
            'description' => 'The estate where the building/rental space is located',
            'required' => ['name', 'address', 'buildings'],
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'A descriptive marketing name of the property'],
                'address' => ['type' => 'string', 'description' => 'Textual address of the estate'],
                //                'images' => [
                //                    'type' => 'array',
                //                    'items' => [
                //                        'type' => 'string',
                //                    ],
                //                    'description' => 'The image urls of the rental space'
                //                ],
                'buildings' => $this->buildingSchema(),
            ],
        ];
    }

    public function buildingsSchema()
    {
        return [
            'type' => 'array',
            'description' => 'The buildings on the estate',
            'items' => [
                'type' => 'object',
                'description' => 'A physical building on the estate',
                'required' => ['name', 'spaces'],
                'properties' => [
                    'name' => ['type' => 'string', 'description' => 'Identifying name of the building (e.g. Hall 1, Building A...)'],
                    'address' => ['type' => 'string', 'description' => 'Address of the building'],
                    //                            'images' => [
                    //                                'type' => 'array',
                    //                                'items' => [
                    //                                    'type' => 'string',
                    //                                ],
                    //                                'description' => 'The image urls of the rental space'
                    //                            ],
                    'spaces' => [
                        'type' => 'array',
                        'items' => $this->rentableSchema(),
                        'description' => 'List of rentable spaces',
                    ],
                ],
            ],
        ];
    }

    public function schema(): array
    {
        return [
            'name' => 'extractData',
            'description' => 'Extract rentable real estate data into the database',
            'required' => ['rentable_units'],
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'buildings' => $this->buildingsSchema(),
                    //                    'rentable_units' => [
                    //                        'type' => 'array',
                    //                        'description' => 'The rentable units to extract',
                    //                        'items' => $this->rentableSchema()
                    //                    ],
                    //                    'buildings' => [
                    //                        'type' => 'array',
                    //                        'description' => 'The rentable units to extract',
                    //                        'items' => [
                    //                            'name' => [
                    //                                'type' => 'string',
                    //                                'description' => 'A name / identifier of the rental space'
                    //                            ],
                    //                            'description' => [
                    //                                'type' => 'string',
                    //                                'description' => 'A detailed real estate description of the rental space'
                    //                            ],
                    //                            'address' => [
                    //                                'type' => 'string',
                    //                            ]
                    //                        ]
                    //                    ]
                    //                    "id": {
                    //                      "type": "string",
                    //                      "description": "The identifier of the rentable"
                    //                    },
                    //                    "name": {
                    //                      "type": "string",
                    //                      "description": "The name / identifier of the rental space"
                    //                    },
                    //                    "description": {
                    //                      "type": "string",
                    //                      "description": "A description of the rental space"
                    //                    },
                    //                    "spaces": {
                    //                      "type": "array",
                    //                      "items": {
                    //                        "$ref": "https://schema.immo/Space"
                    //                      },
                    //                      "description": "The spaces in the rental space"
                    //                    },
                    //                    "images": {
                    //                      "type": "array",
                    //                      "items": {
                    //                        "$ref": "https://schema.immo/Image"
                    //                      },
                    //                      "description": "The images of the rental space"
                    //                    },
                    //                    "floorplans": {
                    //                      "type": "array",
                    //                      "items": {
                    //                        "$ref": "https://schema.immo/Floorplan"
                    //                      },
                    //                      "description": "The floorplans of the rental space"
                    //                    },
                    //                    "features": {
                    //                      "$ref": "https://schema.immo/FeatureList",
                    //                      "description": "The features of the rental space"
                    //                    },
                    //                    "building": {
                    //                      "$ref": "https://schema.immo/Building",
                    //                      "description": "The building where the rental space is located"
                    //                    },
                    //                    "estate": {
                    //                      "$ref": "https://schema.immo/Estate",
                    //                      "description": "The estate where the building/rental space is located"
                    //                    },

                    //                    'building' => [
                    //                        '$ref' => 'https://schema.immo/Building',
                    //                        'description' => 'The building where the rental space is located'
                    //                    ],
                    //                    'estate' => [
                    //                        '$ref' => 'https://schema.immo/Estate',
                    //                        'description' => 'The estate where the building/rental space is located'
                    //                    ]
                ],
            ],
        ];
    }

    public function validate(array $data): array
    {
        //        $validator = validator($data, [
        //            'a' => 'required|numeric',
        //            'b' => 'required|numeric'
        //        ]);
        //
        //        $validator->validate();
        //
        //        return $validator->validated();
        return $data;
    }

    public function execute(array $data): mixed
    {
        dd('execute extract', $data);
    }

    public function callback(): \Closure
    {
        //        return function (int|float $a, int|float $b): int|float {
        //            return $a + $b;
        //        };
    }
}

//"id" => [
//                        'description' => 'The unique identifier for this estate',
//                        'type' => 'string'
//                    ],
//                    'name' => [
//                        'description' => 'Name of the real estate',
//                        'type' => 'string'
//                    ],
//                    'buildings' => [
//                        'description' => 'The buildings on the estate',
//                        'type' => 'array',
//                        'items' => [
//                            "id" => [
//                              "description" => "The unique identifier for this building (UUID)",
//                              "type" => "string"
//                            ],
//                            "name" => [
//                              "description" => "Name of the building",
//                              "type" => "string"
//                            ],
//                            "address" => [
//                              "description" => "Address of the building",
//                              '$ref' => "https://schema.immo/Address"
//                            ],
//                            "spaces" => [
//                              "description" => "List of rentable units",
//                              "type" => "array",
//                              "items" => [
//                                '$ref' => "https://schema.immo/Rentable"
//                              ]
//                            ],
//                            "features" => [
//                              '$ref' => "https://schema.immo/FeatureList",
//                              "description" => "The features of the rental space"
//                            ],
//                            "estate" => [
//                              '$ref' => "https://schema.immo/Estate",
//                              "description" => "The estate where the building/rental space is located"
//                            ]
//                        ]
//                    ],
//                    'address' => [
//                        'description' => 'The address of the estate',
//                        '$ref' => 'https://schema.immo/Address'
//                    ],
//                    'features' => [
//                        '$ref' => 'https://schema.immo/FeatureList',
//                        'description' => 'The features of the rental space'
//                    ]
