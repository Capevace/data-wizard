@props([
    'name' => 'root',
    'statePath' => '.',
    'schema' => [
        'type' => 'object',
        'properties' => [],
        'required' => [],
    ],
])

<?php
$properties = $schema['properties'] ?? [];
$required = collect($schema['required'] ?? []);
?>


@foreach($properties as $property => $propertySchema)

@endforeach

@if ($schema['type'] === 'object')

@elseif ($schema['type'] === 'array')

@else
    <x-resource.json-schema.property :name="$name" :schema="$schema" />
@endif

<any name="root" state-path="." :schema="['type' => 'object', 'properties' => ['test' => ['type' => 'string']]]">
    <x-resource.assign name="root_test" state-path="root?.test">
        <input x-model="root_test" />
    </x-resource.assign>
</any>


<any name="root" state-path="." :schema="['type' => 'object', 'properties' => ['tests' => ['type' => 'array', 'items' => ['type' => 'string']]]]">
    <x-resource.assign name="root_tests" state-path="root?.tests">
        <x-previews.loop name="root_tests" state-path="root?.tests">
            <input x-model="root_tests.0" />
            <input x-model="root_tests.1" />
            <input x-model="root_tests.2" />
        </x-previews.loop>
    </x-resource.assign>
</any>
