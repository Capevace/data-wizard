@props([
    'color' => 'gray',
    'label' => 'Daten',
    'name',
    'statePath',
    'schema' => [
        'type' => 'string',
        'enum' => ['test'],
    ],
    'required' => false,
])

<?php
  $matchesTypes = function (array|string $type, array $typesToMatch) {
      if (is_array($type)) {
          $type = $type[0];
      }

      return in_array($type, $typesToMatch);
  };
?>

<div {{ $attributes }}>
    @if ($schema['type'] === 'array')
        <div class="grid grid-cols-1 gap-4 col-span-full">
            @if ($description = $schema['description'] ?? null)
                <p class="text-sm text-gray-500 dark:text-gray-400 col-span-full">
                    {{ $description }}
                </p>
            @endif

            <x-previews.loop :name="$name" :state-path="$statePath">
                <x-resource.json-schema.property
                    class="col-span-full"
                    :label="\Illuminate\Support\Str::singular($label)"
                    :name="$name . '_0'"
                    :state-path="$statePath . '[' . $name . '_index]'"
                    :schema="$schema['items']"
                    :required="true"
                />
            </x-previews.loop>
        </div>
    @elseif ($matchesTypes($schema['type'], ['object']))
        <article @class(["grid grid-cols-2 gap-x-5 border border-sdashed border-gray-700 border-{$color}-700 bg-gradient-to-br from-{$color}-800/50 to-{$color}-900/50 rounded"])>
            <header class="border-b border-{{ $color }}-700 flex items-center justify-between col-span-full px-3 py-2">
                <h3 class="text-sm font-semibold">{{ \Illuminate\Support\Str::title($label) }}</h3>
            </header>

            @if ($description = $schema['description'] ?? null)
                <p class="text-sm text-gray-500 dark:text-gray-400 col-span-full px-5 py-3">
                    {{ $description }}
                </p>
            @endif

            @foreach($schema['properties'] as $property => $propertySchema)
                <div
                    wire:key="{{ $name . '_' . $property }}"
                    x-data="{ '{{ $name . '_' . $property }}': '{{ $statePath }}.{{ $property }}' }"
                    @class([
                        'px-5 py-3',
                        'col-span-1' => !$matchesTypes($propertySchema['type'], ['object', 'array']),
                        'col-span-full' => $matchesTypes($propertySchema['type'], ['object', 'array']),
                    ])
                >
                    <x-resource.json-schema.property
                        :label="$property"
                        :name="$name . '_' . $property"
                        :state-path="$statePath . '.' . $property"
                        :schema="$propertySchema"
                        :required="collect($schema['required'])->contains($property)"
                    />
                </div>
            @endforeach
        </article>
    @elseif ($matchesTypes($schema['type'], ['integer', 'number', 'float', 'string']))
        <x-filament-forms::field-wrapper.label :$required>
            {{ \Illuminate\Support\Str::title($label) }}
        </x-filament-forms::field-wrapper.label>
        <x-filament::input.wrapper>
            <x-filament::input
                x-model="{{ $statePath }}"
                :$required
                :type="match (true) {
                    $matchesTypes($schema['type'], ['integer', 'number', 'float']) => 'number',
                    $matchesTypes($schema['type'], ['string']) => 'text',
                    default => 'text',
                }"
                :minlength="match (true) {
                    $matchesTypes($schema['type'], ['string']) => $schema['minLength'] ?? null,
                    default => null,
                }"
                :maxlength="match ($schema['type']) {
                    $matchesTypes($schema['type'], ['string']) => $schema['maxLength'] ?? null,
                    default => null,
                }"
                :min="match ($schema['type']) {
                    $matchesTypes($schema['type'], ['integer', 'number', 'float']) => $schema['minimum'] ?? null,
                    default => null,
                }"
                :max="match ($schema['type']) {
                    $matchesTypes($schema['type'], ['integer', 'number', 'float']) => $schema['maximum'] ?? null,
                    default => null,
                }"
                :step="match ($schema['type']) {
                    $matchesTypes($schema['type'], ['integer']) => $schema['multipleOf'] ?? 1,
                    $matchesTypes($schema['type'], ['number', 'float']) => $schema['multipleOf'] ?? null,
                    default => null,
                }"
                :placeholder="$name"
            />
        </x-filament::input.wrapper>
    @else
        <pre class="text-red-500">{{ json_encode([$name, $schema]) }}</pre>
    @endif
</div>
