# Better JSON Schema support for Laravel

This package provides a better JSON Schema support for Laravel.


```php
use Mateffy\JsonSchema;

/*
 * CREATING OR IMPORTING A SCHEMA 
 */
 
// Create a schema. Will be syntax validated
$schema = JsonSchema::fromPath('/path/to/schema.json');
$schema = JsonSchema::fromUrl('https://json-schema.org/draft/2020-12/schema');
$schema = JsonSchema::from($schema); // Clone the schema instance
$schema = JsonSchema::from([
    'type' => 'object',
    'properties' => [
        'name' => [
            'type' => 'string',
            'description' => 'The name of the product',
        ],
        'price' => [
            'type' => 'number',
            'description' => 'The price in EUR',
        ],
    ],
]);
$schema = JsonSchema::fromFilamentForm([/** form schema */]);
$schema = JsonSchema::fromFilamentTable([/** form schema */]);

// Create an object schema with the properties.
// Optionally, description, required etc. can be set as function arguments
$product = JsonSchema::object(
    properties: [
        'name' => ['type' => 'string'],
        'price' => ['type' => 'number'],
    ],
    required: ['name', 'price'],
    description: 'A product',
);

// Wrap a schema in an array schema
$products = JsonSchema::array($product);
$ids = JsonSchema::array(['type' => 'string', 'format' => 'uuid']);


// Premade schemas for that good autocomplete support
JsonSchema::object([
    'name' => JsonSchema::string(
        description: 'The name of the product',
    ),
    'price' => JsonSchema::float(
        description: 'The price in EUR',
        minimum: 0,
    ),
    'details-table' => JsonSchema::object(
        additionalProperties: JsonSchema::string(),
    )
], required: ['name']);


/*
 * GETTING A JSON SCHEMA DEFINITION
 */

// Get the JSON Schema as an array
$schema->toArray();
// -> ['type' => 'object', 'properties' [...]]

// Get the JSON Schema as a JSON string
$schema->toJson();
// -> '{"type":"object","properties": {...}}'

// Get the JSON Schema as a JSON string
$schema->toJson(flags: JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
// -> '{\n  "type": "object",\n  "properties": {\n...\n}\n}'


/*
 * VALIDATING DATA
 */

// Validate some data. Throws a ValidationException if invalid.
// If clean: false is passed, the data will not be modified, only validated
// Data cleaning means removing any additional properties, if not allowed.
// If cleaning is disabled, a ValidationException will be thrown if there are additional properties.
$data = $schema->validate([
    'name' => 'My product',
    'price' => 100,
], clean: false);

// Helper method (returns bool)
$schema->invalid([
    'name' => 'My product',
    'price' => 100,
], strict: true);

// Validate an array of the data defined in the schema, to avoid needing to create a new schema for each item
$data = $schema->validateMany([
    ['name' => 'My product', 'price' => 100],
    ['name' => 'My product', 'price' => 100],
], clean: false);


// Get a Laravel validator instance configured with the schema
$schema->toValidator();


// Filament Forms
$components = $schema->toFilamentFormSchema();
// -> [TextInput::class, TextInput::class]
$component = $schema->toFilamentFormComponent();
// -> TextInput::class etc for single schema or Grid::class when array or object

$columns = $schema->toFilamentTableColumns();
$column = $schema->toFilamentTableColumn();

$components = $schema->toFilamentInfolistSchema();
// -> [TextEntry::class, TextEntry::class]
$component = $schema->toFilamentInfolistComponent();
// -> TextEntry::class etc for single schema or Grid::class when array or object
```
