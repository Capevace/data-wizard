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
    'disabled' => false,
])

<?php
  $matchesTypes = function (array|string $type, array $typesToMatch) {
      if (is_array($type)) {
          $type = $type[0];
      }

      return in_array($type, $typesToMatch);
  };

  $getMagicUiTableConfig = function (array $schema) {
      $config = $schema['magic_ui'] ?? null;

      if (is_array($config) && $config['type'] === 'table') {
          return $config;
      } else if ($config === 'table') {
          return ['type' => 'table'];
      }
  };

  $getMagicUiLabel = function (array $schema) {
      $config = $schema['magic_ui'] ?? null;

      if (is_array($config) && $config['label'] ?? null) {
          return $config['label'];
      }

      return null;
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

            @if ($table = $getMagicUiTableConfig($schema))
                <x-resource.json-schema.table :name="$name" :state-path="$statePath" :schema="$schema['items']" :disabled="$disabled" />
            @else
                <x-previews.loop :name="$name" :state-path="$statePath" class="grid gap-5">
                    <x-resource.json-schema.property
                        x-bind:id="{{ $name }}_index . '_' . hashObject({{ $name }})"
                        class="col-span-full"
                        :label="$getMagicUiLabel($schema) ?? \Illuminate\Support\Str::singular($label)"
                        :name="$name . '_0'"
                        :state-path="$statePath . '[' . $name . '_index]'"
                        :schema="$schema['items']"
                        :required="true"
                        :disabled="$disabled"
                    />
                </x-previews.loop>
            @endif
        </div>
    @elseif ($matchesTypes($schema['type'], ['object']))
        @if (count($schema['properties'] ?? []) === 1 && ($property = array_keys($schema['properties'] ?? [])[0]))
            <x-resource.json-schema.property
                :label="$property"
                :name="$name . '_' . $property"
                :state-path="$statePath . '.' . $property"
                :schema="$schema['properties'] ?? [][$property]"
                :required="collect($schema['required'])->contains($property)"
                :disabled="$disabled"
            />
        @else
            <article @class(["shadow-sm grid grid-cols-2 gap-x-5 border border-{$color}-400/30 dark:border-{$color}-700 bg-gradient-to-br from-{$color}-50/80 to-{$color}-200/80 dark:from-{$color}-800/50 dark:to-{$color}-900/50 rounded"])>
                <header class="border-b border-{{ $color }}-400/50 dark:border-{{ $color }}-700 flex items-center justify-between col-span-full px-3 py-2">
                    <h3 class="text-sm font-semibold">{{ $getMagicUiLabel($schema) ?? str($label)->singular()->title() }}</h3>
                </header>

                @if ($description = $schema['description'] ?? null)
                    <p class="text-sm text-gray-500 dark:text-gray-400 col-span-full px-5 py-3">
                        {{ $description }}
                    </p>
                @endif

                @foreach($schema['properties'] ?? [] as $property => $propertySchema)
                    <div
                        wire:key="{{ $name . '_' . $property }}"
                        x-data="{ '{{ $name . '_' . $property }}': '{{ $statePath }}.{{ $property }}' }"
                        @class([
                            'px-5 py-3',
                            'col-span-1' => !$matchesTypes($propertySchema['type'] ?? '', ['object', 'array']),
                            'col-span-full' => $matchesTypes($propertySchema['type'] ?? '', ['object', 'array']),
                        ])
                    >
                        <x-resource.json-schema.property
                            :label="$property"
                            :name="$name . '_' . $property"
                            :state-path="$statePath . '.' . $property"
                            :schema="$propertySchema"
                            :required="collect($schema['required'] ?? [])->contains($property)"
                            :disabled="$disabled"
                        />
                    </div>
                @endforeach
            </article>
        @endif
    @elseif ($matchesTypes($schema['type'], ['integer', 'number', 'float', 'string']))
        <x-filament-forms::field-wrapper.label :$required>
            {{ $getMagicUiLabel($schema) ?? \Illuminate\Support\Str::title($label) }}
        </x-filament-forms::field-wrapper.label>

        @if (data_get($schema, 'magic_ui.component') === 'textarea' || data_get($schema, 'magic_ui') === 'textarea')
            <x-filament::input.wrapper class="fi-fo-textarea overflow-hidden">
                <textarea
                    name="{{ $statePath }}"
                    x-model="{{ $statePath }}"
                    @if ($disabled)
                        disabled
                    @endif

                    @if ($required)
                        required
                    @endif

                    @if ($matchesTypes($schema['type'], ['string']) && ($schema['minLength'] ?? null))
                        minlength="{{ $schema['minLength'] }}"
                    @endif

                    @if ($matchesTypes($schema['type'], ['string']) && ($schema['maxLength'] ?? null))
                        maxlength="{{ $schema['maxLength'] }}"
                    @endif

                    placeholder="{{ $name }}"

                    @class([
                        'block w-full h-full border-none bg-transparent px-3 py-1.5 text-base text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6',
                    ])
                ></textarea>
            </x-filament::input.wrapper>
        @elseif ($enum = ($schema['enum'] ?? null))
            <x-filament::input.wrapper>
                <x-filament::input.select
                    name="{{ $statePath }}"
                    x-model="{{ $statePath }}"

                    :disabled="$disabled ? true : false"
                    :required="$required ? true : false"
                >
                    @foreach ($enum as $value)
                        <option
                            wire:key="{{ $value }}"
                            value="{{ $value }}"
                        >{{ $value }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        @else
            <x-filament::input.wrapper>
                <x-filament::input
                    name="{{ $statePath }}"
                    x-model="{{ $statePath }}"
                    :$disabled
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
        @endif
    @elseif ($matchesTypes($schema['type'], ['boolean']))
        <x-filament-forms::field-wrapper.label :$required>
            <div class="inline-flex items-center gap-2">
                <x-filament::input.checkbox
                    x-model="{{ $statePath }}"
                    :$disabled
                    :$required
                    :label="$name"
                />

                <span>{{ $getMagicUiLabel($schema) ?? \Illuminate\Support\Str::title($label) }}</span>
            </div>
        </x-filament-forms::field-wrapper.label>
    @else
        <pre class="text-red-500">{{ json_encode([$name, $schema]) }}</pre>
    @endif
</div>
