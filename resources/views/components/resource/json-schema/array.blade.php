@props([
    'name' => 'root',
    'statePath' => '.',
    'items' => [
        'type' => 'object',
        'properties' => [],
        'required' => [],
    ],
])

<x-previews.loop :$name :$statePath>
    <x-resource.json-schema.object :schema="$items" />
</x-previews.loop>
