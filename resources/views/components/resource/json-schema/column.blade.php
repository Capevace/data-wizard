@props([
    'color' => 'gray',
    'label' => 'Daten',
    'column',
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
?>

<div {{ $attributes->class('h-full') }}>
    @if ($schema['type'] === 'array')
        <div class="text-xs">array</div>
{{--        <div class="grid grid-cols-1 gap-4 col-span-full">--}}
{{--            @if ($description = $schema['description'] ?? null)--}}
{{--                <p class="text-sm text-gray-500 dark:text-gray-400 col-span-full">--}}
{{--                    {{ $description }}--}}
{{--                </p>--}}
{{--            @endif--}}

{{--            @if ($schema['magic_ui'] ?? false === 'table')--}}
{{--                <x-llm-magic::resource.json-schema.table :name="$name" :state-path="$statePath" :schema="$schema['items']" :disabled="$disabled" />--}}
{{--            @else--}}
{{--                <x-llm-magic::previews.loop :name="$name" :state-path="$statePath">--}}
{{--                    <x-llm-magic::resource.json-schema.property--}}
{{--                        class="col-span-full"--}}
{{--                        :label="\Illuminate\Support\Str::singular($label)"--}}
{{--                        :name="$name . '_0'"--}}
{{--                        :state-path="$statePath . '[' . $name . '_index]'"--}}
{{--                        :schema="$schema['items']"--}}
{{--                        :required="true"--}}
{{--                        :disabled="$disabled"--}}
{{--                    />--}}
{{--                </x-llm-magic::previews.loop>--}}
{{--            @endif--}}
{{--        </div>--}}
    @elseif ($matchesTypes($schema['type'], ['object']))
        <div class="text-xs text-danger-400 dark:text-danger-700">object</div>
    @elseif ($matchesTypes($schema['type'], ['integer', 'number', 'float', 'string']))
{{--        <x-filament::input.wrapper>--}}
            <x-filament::input
                x-data="{ focused: false }"
                @focus="{{ $column }}_focused = true; focused = true"
                @blur="{{ $column }}_focused = false; focused = false"
                class="!py-0 h-full !pl-2 !pr-2 !text-xs min-w-32"
                x-bind:class="{
                    'shadow-inner': focused,
                }"
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
{{--        </x-filament::input.wrapper>--}}
    @else
        <pre class="text-red-500">{{ json_encode([$name, $schema]) }}</pre>
    @endif
</div>
