<?php

return [
    "type" => "object",
    "properties" => [
        "estates" => [
            "type" => "array",
            "items" => [
                "type" => "object",
                "properties" => [
                    "type" => "object",
                    "description" =>
                        "The estate where the building/rental space is located",
                    "required" => ["name", "address", "buildings"],
                    "properties" => [
                        "name" => [
                            "type" => "string",
                            "description" =>
                                "A descriptive marketing name of the property",
                        ],
                        "address" => [
                            "type" => "string",
                            "description" => "Textual address of the estate",
                        ],
                        "images" => [
                            "type" => "array",
                            "items" => [
                                "type" => "string",
                            ],
                            "description" =>
                                "The image urls of the rental space",
                        ],
                        "buildings" => [
                            "type" => "array",
                            "description" => "The buildings on the estate",
                            "items" => [
                                "type" => "object",
                                "description" =>
                                    "A physical building on the estate",
                                "required" => ["name", "spaces"],
                                "properties" => [
                                    "name" => [
                                        "type" => "string",
                                        "description" =>
                                            "Identifying name of the building (e.g. Hall 1, Building A...)",
                                    ],
                                    "address" => [
                                        "type" => "string",
                                        "description" =>
                                            "Address of the building",
                                    ],
                                    "images" => [
                                        "type" => "array",
                                        "items" => [
                                            "type" => "string",
                                        ],
                                        "description" =>
                                            "The image urls of the rental space",
                                    ],
                                    "spaces" => [
                                        "type" => "array",
                                        "description" =>
                                            "List of rentable spaces",
                                        "items" => [
                                            "type" => "object",
                                            "description" =>
                                                "A singular rentable / physical space. Smallest possible unit.",
                                            "required" => [
                                                "name",
                                                "description",
                                                "type",
                                                "area",
                                                "features",
                                            ],
                                            "properties" => [
                                                "id" => [
                                                    "type" => "string",
                                                    "description" =>
                                                        "The unique identifier for this rentable unit, if you think this rentable unit is already in your memory. Otherwise, leave it empty.",
                                                ],
                                                "name" => [
                                                    "type" => "string",
                                                    "description" =>
                                                        "A name / identifier of the rental space",
                                                ],
                                                "description" => [
                                                    "type" => "string",
                                                    "description" =>
                                                        "A detailed real estate description of the rental space",
                                                ],
                                                "type" => [
                                                    "type" => "string",
                                                    "description" =>
                                                        "The type of the space",
                                                    "enum" => [
                                                        "office",
                                                        "retail",
                                                        "storage",
                                                        "living",
                                                        "other",
                                                    ],
                                                ],
                                                "area" => [
                                                    "type" => "number",
                                                    "description" =>
                                                        "The area of the space",
                                                ],
                                                "floor" => [
                                                    "type" => "number",
                                                    "description" =>
                                                        "The floor (0 = EG, -1 = 1. UG, 1 = 1. OG, ...)",
                                                ],
                                                "rent_per_m2" => [
                                                    "type" => "number",
                                                    "description" =>
                                                        "The rent per square meter of the space",
                                                ],
                                                "rent_total" => [
                                                    "type" => "number",
                                                    "description" =>
                                                        "The total rent of the space",
                                                ],
                                                "images" => [
                                                    "type" => "array",
                                                    "items" => [
                                                        "type" => "string",
                                                    ],
                                                    "description" =>
                                                        "The image urls of the rental space",
                                                ],
                                                "floorplans" => [
                                                    "type" => "array",
                                                    "items" => [
                                                        "type" => "string",
                                                    ],
                                                    "description" =>
                                                        "The floorplan urls of the rental space",
                                                ],
                                                "features" => [
                                                    "description" =>
                                                        "The features of the rental space",
                                                    "type" => "array",
                                                    "items" => [
                                                        "type" => "string",
                                                        "enum" => [
                                                            "access-control",
                                                            "acoustic-ceiling",
                                                            "address",
                                                            "air-conditioning",
                                                            "balcony",
                                                            "bees",
                                                            "bike-slots",
                                                            "block-heating",
                                                            "cctv",
                                                            "cantina",
                                                            "carpet-flooring",
                                                            "carsharing",
                                                            "central-fire-alarm-system",
                                                            "central-fire-alarm-system-fire-department",
                                                            "central-heating",
                                                            "ceramic-facade",
                                                            "cleaning-service",
                                                            "close-to-public-transport",
                                                            "completely-renovated",
                                                            "cooled-halls",
                                                            "cooling",
                                                            "custom",
                                                            "delivery-for-24h",
                                                            "distance-airport",
                                                            "distance-main-station",
                                                            "distance-port",
                                                            "district-heating",
                                                            "double-floor",
                                                            "dressing-room",
                                                            "electric-heating",
                                                            "electric-vehicle-charging-station",
                                                            "electronic-lock",
                                                            "elevators",
                                                            "fiberglass-connection",
                                                            "floor-heating",
                                                            "floored-windows",
                                                            "floors",
                                                            "gas-heating",
                                                            "gastronomy-on-site",
                                                            "geo-thermal",
                                                            "green-courtyard",
                                                            "green-lease-contracts",
                                                            "greened-facade",
                                                            "greened-roof",
                                                            "ground-level-access",
                                                            "guarded",
                                                            "handicap-accessible",
                                                            "handicap-fully-accessible",
                                                            "hardwood-flooring",
                                                            "heat-pump",
                                                            "heated-halls",
                                                            "heavy-duty-crane",
                                                            "heavy-duty-elevator",
                                                            "high-ceilings",
                                                            "high-voltage-outlet",
                                                            "hydraulic-lift",
                                                            "individually-expandable",
                                                            "infrared-heating",
                                                            "kindergarden",
                                                            "kitchen",
                                                            "lkw-slots",
                                                            "laboratory-s1",
                                                            "laboratory-s2",
                                                            "led-lighting",
                                                            "lighting-with-motion-detection",
                                                            "marketing-website",
                                                            "max-floor-load",
                                                            "maximum-surface-desealing",
                                                            "meeting-rooms",
                                                            "modernized",
                                                            "monument-protection",
                                                            "nearby-sports",
                                                            "needs-renovation",
                                                            "nesting-box",
                                                            "new-construction",
                                                            "number-of-rooms",
                                                            "number-of-workplaces",
                                                            "open-rooms",
                                                            "outdoor-sunshade",
                                                            "outdoor-sunshade-electric",
                                                            "pkw-slots",
                                                            "parking-garage",
                                                            "places-for-socializing",
                                                            "plot-area",
                                                            "ramp-access",
                                                            "receptionist",
                                                            "recycling",
                                                            "rentable-area",
                                                            "representative-foyer",
                                                            "revitalized",
                                                            "rolling-gates",
                                                            "roof-terrace",
                                                            "server-room",
                                                            "shower",
                                                            "smart-metering",
                                                            "social-tenant",
                                                            "solar-energy",
                                                            "solar-heating",
                                                            "sprinkler-system",
                                                            "storage-rack",
                                                            "storey-heating",
                                                            "tee-kitchen",
                                                            "think-tanks",
                                                            "urban-gardening",
                                                            "variable-room-division",
                                                            "ventilation",
                                                            "washing-clothes",
                                                            "water-retention",
                                                            "wild-flower-grasses",
                                                            "wind-energy",
                                                            "wired-with-cat7",
                                                            "wood-hybrid-construction",
                                                            "wooden-façade",
                                                            "year-of-construction",
                                                            "year-of-renovation",
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
