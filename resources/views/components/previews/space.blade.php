{{--
Passed via Alpine:
- estate
- estateIndex
- space
- spaceIndex
--}}

<x-llm-magic::resource heading="Fläche" x-bind:key="spaceIndex" color="lime">
    <div class="grid grid-cols-2 gap-2">
        <template x-if="space.name">
            <x-llm-magic::resource.alpine-field label="Name" bind-value="estate.name" />
        </template>

        <template x-if="space.description">
            <x-llm-magic::resource.alpine-field label="Beschreibung" bind-value="space.description" />
        </template>

        <template x-if="space.type">
            <x-llm-magic::resource.alpine-field label="Typ" bind-value="space.type" />
        </template>

        <template x-if="space.area">
            <x-llm-magic::resource.alpine-field label="Fläche" bind-value="space.area" />
        </template>

        <template x-if="space.floor">
            <x-llm-magic::resource.alpine-field label="Etage" bind-value="space.floor" />
        </template>

        <template x-if="space.rent_per_m2">
            <x-llm-magic::resource.alpine-field label="Miete pro m²" bind-value="space.rent_per_m2" />
        </template>

        <template x-if="space.rent_total">
            <x-llm-magic::resource.alpine-field label="Miete pro m²" bind-value="space.rent_per_m2" />
        </template>
    </div>

    <template x-if="space.images">
        <x-llm-magic::resource.sub-resources label="Bilder">
            <template x-for="(image, index) in space.images">
                <img
                    :key="index"
                    :src="`/api/dataset/${dataset}${image}`"
                />
            </template>
        </x-llm-magic::resource.sub-resources>
    </template>

    <template x-if="space.floorplans">
        <x-llm-magic::resource.sub-resources label="Bilder">
            <template x-for="(image, index) in space.floorplans">
                <img
                    :key="index"
                    :src="`/api/dataset/${dataset}${image}`"
                />
            </template>
        </x-llm-magic::resource.sub-resources>
    </template>

    <template x-if="space.features">
        <x-llm-magic::resource.sub-resources label="Bilder">
            <template x-for="(feature, index) in space.features">
                <p :key="index" x-text="feature"></p>
            </template>
        </x-llm-magic::resource.sub-resources>
    </template>
</x-llm-magic::resource>
