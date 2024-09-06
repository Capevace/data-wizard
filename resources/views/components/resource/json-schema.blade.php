@props([
    'schema',
])

<div class="grid grid-cols-1 gap-2">
    <x-resource.json-schema.property name="root" state-path="$wire.partialResultJson" :schema="$schema" />
</div>

