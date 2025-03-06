@props([
    'schema',
    'statePath' => '$wire.resultData',
    'disabled' => false,
])

<div class="grid grid-cols-1 gap-2">
    <x-llm-magic::resource.json-schema.property name="root" :$statePath :$schema :$disabled />
</div>

