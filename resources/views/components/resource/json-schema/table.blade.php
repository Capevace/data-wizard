@props([
    'name',
    'heading' => null,
    'statePath' => '',
    'schema' => [],
    'disabled' => false,
])


<div class="relative w-full flex flex-col h-max" x-data="{ sort: null, sortDirection: 'asc' }">
    <div class="flex flex-column sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4">
        <x-filament::dropdown placement="bottom-start">
            <x-slot:trigger>
                <x-filament::button
                    color="gray"
                    size="sm"
                    icon="heroicon-o-bars-arrow-down"
                    class="flex items-center justify-between"
                >
                    <div class="flex items-center gap-2">
                        <span>Sortieren nach</span>
                        <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
                    </div>
                </x-filament::button>
            </x-slot:trigger>

            <x-filament::dropdown.list>
                @foreach($schema['properties'] as $property => $propertySchema)
                    <x-filament::dropdown.list.item
                        wire:key="{{ $name . $property }}"
                        @click="
                            if (sort) {
                                sort = null;
                            } else {
                                sort = '{{ $property }}'
                            }
                        "
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span>{{ \Illuminate\Support\Str::title($property) }}</span>
                            <x-icon
                                name="heroicon-o-check"
                                class="w-4 h-4 text-success-500 dark:text-success-400 flex-shrink-0"
                                x-show="sort === '{{ $property }}'"
                            />
                        </div>
                    </x-filament::dropdown.list.item>
                @endforeach
            </x-filament::dropdown.list>
        </x-filament::dropdown>
        <label for="table-search" class="sr-only">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 rtl:inset-r-0 rtl:right-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
            </div>
            <input type="text" id="table-search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
        </div>
    </div>
    <div class="overflow-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 flex-1">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 rounded-t-lg border-b border-gray-200 dark:border-gray-700">
                <tr>
{{--                    <td class="rounded-tl-lg"></td>--}}

                    @foreach($schema['properties'] as $property => $propertySchema)
                        <td
                            wire:key="{{ $name . $property }}"
                            x-data="{ '{{ $name . $property }}': '{{ $statePath }}.{{ $property }}' }"
                            @class([
                                'px-3 py-2 text-sm',
                                'rounded-tl-lg' => $loop->first,
                            ])
                        >
                            <x-llm-magic::resource.json-schema.column-header
                                :label="$property"
                                :name="$name . '_' . $property"
                                :state-path="$statePath . '.' . $property"
                                :schema="$propertySchema"
                                :required="collect($schema['required'] ?? [])->contains($property)"
                                :disabled="$disabled"
                            />
                        </td>
                    @endforeach

                    <td class="rounded-tr-lg"></td>
                </tr>
            </thead>
            <x-llm-magic::previews.loop :name="$name" :state-path="$statePath" sorted-by-js tag="tbody" class="rounded-b-lg">
                <tr
                    class="border-b  dark:border-gray-700 "
                    :id="{{ $name }}_index + '_' + hashObject({{ $name }})"
                    x-data="{ {{ $name }}_focused: false }"
                    :class="{
                        'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700': !{{ $name }}_focused,
                        'bg-gray-50 dark:bg-gray-900': {{ $name }}_focused,
                    }"
                >
{{--                    <td class="w-4 px-4 py-2">--}}
{{--                        <div class="flex items-center">--}}
{{--                            <input id="checkbox-table-search-1" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">--}}
{{--                            <label for="checkbox-table-search-1" class="sr-only">checkbox</label>--}}
{{--                        </div>--}}
{{--                    </td>--}}
                    @foreach($schema['properties'] as $property => $propertySchema)
                        <td
                            wire:key="{{ $name . '_' . $property }}"
                            x-data="{ '{{ $name . '_' . $property }}': '{{ $statePath }}.{{ $property }}' }"
                            @class([
                                'hover:shadow-inner h-8 border-l',
                                'border-gray-200 dark:border-gray-700' => !$loop->first,
                            ])
                        >
                            <x-llm-magic::resource.json-schema.column
                                :label="$property"
                                :column="$name"
                                :name="$name . '_' . $property"
                                :state-path="$statePath . '[' . $name . '_index]' . '.' . $property"
                                :schema="$propertySchema"
                                :required="collect($schema['required'] ?? [])->contains($property)"
                                :disabled="$disabled"
                            />
                        </td>
                    @endforeach

                    <td class="pl-6 pr-4">
                        <x-filament::icon-button
                            icon="heroicon-o-x-mark"
                            color="danger"
                            size="sm"
                            class="w-full"
                            tooltip="Remove row"
                        />
                    </td>
                </tr>
            </x-llm-magic::previews.loop>
        </table>
    </div>
</div>
