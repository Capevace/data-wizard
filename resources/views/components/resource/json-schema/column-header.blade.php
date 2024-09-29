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
<div
    type="button"
    class="cursor-pointer flex items-center gap-2 justify-between"
    @click="
        if (sort === '{{ $label }}' && sortDirection === 'asc') {
            sortDirection = 'desc';
        } else if (sort === '{{ $label }}' && sortDirection === 'desc') {
            sort = null;
            sortDirection = 'asc';
        } else if (sort !== '{{ $label }}') {
            sort = '{{ $label }}';
            sortDirection = 'asc';
        }
    "
>
    <x-filament-forms::field-wrapper.label :$required>
        {{ \Illuminate\Support\Str::title($label) }}
    </x-filament-forms::field-wrapper.label>

    <nav x-show="sort === '{{ $label }}'" class="flex-shrink-0 flex items-center gap-2">
        <x-filament::icon-button
            icon="heroicon-o-bars-arrow-down"
            color="danger"
            size="xs"
            x-show="sortDirection === 'asc'"
        />

        <x-filament::icon-button
            icon="heroicon-o-bars-arrow-up"
            color="danger"
            size="xs"
            x-show="sortDirection === 'desc'"
        />
    </nav>
</div>
