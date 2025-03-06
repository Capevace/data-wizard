{{--
Passed via Alpine:
- estate
- estateIndex
- building
- buildingIndex
--}}

<x-llm-magic::resource heading="Gebäude" x-bind:key="buildingIndex" color="teal">
    <template x-if="building.name">
        <x-llm-magic::resource.alpine-field label="Name" bind-value="estate.name" />
    </template>

    <template x-if="building.description">
        <x-llm-magic::resource.alpine-field label="Beschreibung" bind-value="building.description" />
    </template>

    <template x-if="building.address">
        <x-llm-magic::resource.alpine-field label="Adresse" bind-value="building.address" />
    </template>

    <template x-if="building.images">
        <x-llm-magic::resource.sub-resources label="Bilder" container-class="grid grid-cols-5 gap-3">
            <template x-for="(image, index) in building.images">
                <img
                    :key="index"
                    :src="`/api/dataset/${dataset}${image}`"
                />
            </template>
        </x-llm-magic::resource.sub-resources>
    </template>

    <template x-if="building.rentables && building.rentables.length > 0">
        <x-llm-magic::resource.sub-resources label="Flächen" container-class="grid grid-cols-3 gap-5">
            <template x-for="(space, spaceIndex) in building.rentables" :key="spaceIndex">
                <x-llm-magic::previews.space />
            </template>
        </x-llm-magic::resource.sub-resources>
    </template>
</x-llm-magic::resource>
