@props([
    'heading' => 'Produkt',
    'color' => 'sky',
])

<x-llm-magic::previews.loop name="product" state-path="products">
    <x-llm-magic::resource :$heading :$color x-bind:key="product_index">
        <x-llm-magic::resource.debug x-model="product" />

        <template x-if="product.name">
            <x-llm-magic::resource.alpine-field label="Name" x-model="product.name" />
        </template>

        <template x-if="product.description">
            <x-llm-magic::resource.alpine-field label="Beschreibung" x-model="product.description" />
        </template>

        <template x-if="product.price">
            <x-llm-magic::resource.alpine-field label="Preis" x-model="product.price" />
        </template>
    </x-llm-magic::resource>

</x-llm-magic::previews.loop>
