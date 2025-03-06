@props([
    'label',
    'description' => null,

    /** @var ?string $icon */
    'icon' => null,
    'chevronIcon' => 'heroicon-o-chevron-down',

    'color' => 'gray',

    'trigger' => null,

    'columns' => [],
    'rows' => [],

    'invocation' => null,
])

<x-llm-magic::tools.dropdown
    :$icon
    :$color
    :$label
    :$description
    :collapsed="false"
>
    <div
        x-data="{
            rows: @js($rows),
            debugModeEnabled: false,

            get data() {
                if (typeof this.rows === 'string') {
                    return JSON.parse(this.rows);
                }

                return this.rows;
            },
        }">
        <x-llm-magic::resource.debug x-model="data" />
        <x-llm-magic::resource.json-schema.table
            name="table"
            state-path="data"
            :schema="[
                'type' => 'object',
                'properties' => collect($columns)
                    ->mapWithKeys(fn (string $column, string $key) => [$key => ['type' => 'string']])
                    ->all(),
            ]"
        />
    </div>
</x-tools.dropdown>
