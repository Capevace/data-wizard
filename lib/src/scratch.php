<?php

namespace Mateffy\Magic;

class MagicImport
{
    public function __construct()
    {
    }
}


// expose1.pdf
// expose2.pdf
// Technical building inspection report.pdf

// Extract text and images

class Resource
{
    protected array $relations = [];
    protected array $fields = [];

    public function relations(array $relations): static
    {
        $this->relations = $relations;

        return $this;
    }

    public function fields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function prepare(array $data): array
    {
        /** @var array<string, mixed> $prepared */
        $prepared = [];

        /** @var array<string, mixed> $rules */
        $rules = [];

        foreach ($this->fields as $field) {
            $rules[$field->name] = $field->rules();
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        foreach ($this->fields as $field) {
            $prepared[$field->name] = $field->prepare($data[$field->name]);
        }

        return $prepared;
    }

    public function toGptSchema(): array
    {
        $properties = [];

        foreach ($this->fields as $field) {
            $properties[$field->name] = $field->toGptSchema();
        }

        return [
            'type' => 'object',
            'properties' => $properties,
            'required' => ['id', 'type', 'data'],
        ];
    }
}

class Field
{
    public function __construct(
        public string $name,
        public string $type,
        public bool $required = false
    ) {
    }

    public function rules(): array
    {
        return [
            $this->required ? 'required' : 'nullable',
        ];
    }

    public function prepare(mixed $value): mixed
    {
        return $value;
    }

    public function toGptSchema(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'required' => $this->required,
        ];
    }

}


class BigPicture
{
    /** @var array<string, ResourceDocument> $resources */
    public array $documents = [];

    public function __construct(
        public string $id,
        public array $resources
    ) {
    }

    /**
     * @param array<ResourceDocument> $documents
     */
    public function import(array $documents)
    {
        // Run LLM extractor on each document

        $schema = [];

        foreach ($this->resources as $resource) {
            $schema[$resource->type] = $resource->toGptSchema();
        }
    }
}

interface Content
{
    public function text(): string;
}

interface PageBasedContent extends Content
{
    public function textOnPage(int $page): string;
}

interface ImageContent extends Content
{
    public function image(): string;
}

readonly class ResourceDocument
{
    protected function __construct(
        public string $id,
        public string $type,
        public array $contents,
        public ?array $data,
        protected mixed $context
    ) {
    }

    public static function new(string $id, string $type, array $data): mixed
    {
        $doc = PersistedResourceDocument::create([
            'id' => $id,
            'type' => $type,
            'data' => $data,
        ]);

        return new static($id, $type, $data, $doc);
    }

    public static function find(string $id, string $type): mixed
    {
        $doc = PersistedResourceDocument::query()
            ->where('id', $id)
            ->where('type', $type)
            ->first();

        if ($doc === null) {
            return null;
        }

        return new static($id, $type, $doc->data, $doc);
    }

    public function update(array $data): static
    {
        $this->context->update([
            'data' => $data,
        ]);

        return new static($this->id, $this->type, $data, $this->context);
    }

    public function delete(): void
    {
        unset($this->data);
    }
}



$resource = Resource::new(Estate::class)
    ->name('Estate')
    ->description('Estate description')
    ->createUsing(fn (array $data): Model => Estate::create($data))
    ->updateUsing(fn (mixed $id, array $data): Model => Estate::find($id)->update($data))
    ->findUsing(fn (mixed $id, ?string $field): ?Model => Estate::find($id))
    ->relations([
        Relation::new('address', Address::class)

    ]);

    ->fields([
        Field::new('name')
            ->type('string')
            ->required(),
        ResourceField::new('address', $resource)
            ->belongsTo(via: 'address_id', foreignKey: 'id')
            ->createUsing(fn (array $data): Model => Address::create($data))
            ->updateUsing(fn (mixed $id, array $data): Model => Address::find($id)->update($data))
            ->findUsing(fn (mixed $id, ?string $field): ?Model => Address::find($id))
            ->required(),
        Field::new('price')
            ->type('number')
            ->required(),
    ]);

// Example usage:

MagicImport::setup(
    resources: [
        EstateResource::class,

    ]
)
    ->import('https://www.example.com/1')
