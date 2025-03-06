{{--
Passed via Alpine:
- estate
- estateIndex
--}}

<x-llm-magic::resource heading="Objekt" x-bind:key="estateIndex" color="sky">
    <template x-if="estate.name">
        <x-llm-magic::resource.alpine-field label="Name" bind-value="estate.name" />
    </template>

    <template x-if="estate.description">
        <x-llm-magic::resource.alpine-field label="Beschreibung" bind-value="estate.description" />
    </template>

    <template x-if="estate.address">
        <x-llm-magic::resource.alpine-field label="Adresse" bind-value="estate.address" />
    </template>

    <template x-if="estate.images">
        <x-llm-magic::resource.sub-resources label="Bilder" container-class="grid grid-cols-5 gap-3">
            <template x-for="(image, index) in estate.images">
                <img
                    :key="index"
                    :src="`/api/dataset/${dataset}${image}`"
                />
            </template>
        </x-llm-magic::resource.sub-resources>
    </template>

    <template x-if="estate.buildings">
        <x-llm-magic::resource.sub-resources label="Gebäude" class="">
            <template x-for="(building, buildingIndex) in estate.buildings">
                <x-llm-magic::previews.building />
            </template>
        </x-llm-magic::resource.sub-resources>
    </template>
</x-llm-magic::resource>
