@props([
    'schema',
    'statePath' => '$wire.resultData',
    'validationStatePath' => '$wire.validationErrors',
    'disabled' => false,
])

<div class="grid grid-cols-1 gap-2">
    <x-llm-magic::resource.json-schema.property
        name="root"
        :$statePath
        :root-state-path="$statePath"
        :validation-root-state-path="$validationStatePath"
        :$schema
        :$disabled
    />
</div>

