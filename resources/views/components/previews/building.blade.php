{{--
Passed via Alpine:
- estate
- estateIndex
- building
- buildingIndex
--}}

<x-resource heading="Gebäude" x-bind:key="buildingIndex" color="teal">
    <template x-if="building.name">
        <x-resource.alpine-field label="Name" bind-value="estate.name" />
    </template>

    <template x-if="building.description">
        <x-resource.alpine-field label="Beschreibung" bind-value="building.description" />
    </template>

    <template x-if="building.address">
        <x-resource.alpine-field label="Adresse" bind-value="building.address" />
    </template>

    <template x-if="building.images">
        <x-resource.sub-resources label="Bilder" container-class="grid grid-cols-5 gap-3">
            <template x-for="(image, index) in building.images">
                <img
                    :key="index"
                    :src="`/api/dataset/${dataset}${image}`"
                />
            </template>
        </x-resource.sub-resources>
    </template>

    <template x-if="building.rentables && building.rentables.length > 0">
        <x-resource.sub-resources label="Flächen" container-class="grid grid-cols-3 gap-5">
            <template x-for="(space, spaceIndex) in building.rentables" :key="spaceIndex">
                <x-previews.space />
            </template>
        </x-resource.sub-resources>
    </template>
</x-resource>
