@props([
    'name' => 'root',
    'statePath' => '.',
    'items' => [
        'type' => 'object',
        'properties' => [],
        'required' => [],
    ],
])

<x-llm-magic::previews.loop :$name :$statePath>
    <x-llm-magic::resource.json-schema.object :schema="$items" />
</x-llm-magic::previews.loop>
