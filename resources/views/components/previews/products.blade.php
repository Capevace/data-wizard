@props([
    'heading' => 'Produkt',
    'color' => 'sky',
])

<x-previews.loop name="product" state-path="products">
    <x-resource :$heading :$color x-bind:key="product_index">
        <x-resource.debug x-model="product" />

        <template x-if="product.name">
            <x-resource.alpine-field label="Name" x-model="product.name" />
        </template>

        <template x-if="product.description">
            <x-resource.alpine-field label="Beschreibung" x-model="product.description" />
        </template>

        <template x-if="product.price">
            <x-resource.alpine-field label="Preis" x-model="product.price" />
        </template>
    </x-resource>

</x-previews.loop>
