@props([
    'heading' => 'Produkt',
    'color' => 'sky',
])
<?php
$field = $attributes->get('x-bind:field', 'product');
$index = $attributes->get('x-bind:key', "{$field}_index");
$name = $attributes->get('x-bind:name', "{$field}.name");
$price = $attributes->get('x-bind:price', "{$field}.price");
$discountedPrice = $attributes->get('x-bind:price', "{$field}.discountedPrice");
$originalPrice = $attributes->get('x-bind:price', "{$field}.originalPrice");
?>
<x-resource :$heading :$color x-bind:key="{!! $index !!}">
    <x-resource.debug x-model="{!! $field !!}" />

    <template x-if="{!! $name !!}">
        <x-resource.alpine-field label="Name" x-model="{!! $name !!}" />
    </template>

    <template x-if="{!! $price !!}">
        <x-resource.alpine-field label="Preis" x-model="{!! $price !!}" />
    </template>

    <template x-if="{!! $discountedPrice !!}">
        <x-resource.alpine-field label="Rabattpreis" x-model="{!! $discountedPrice !!}" />
    </template>
    <template x-if="{!! $originalPrice !!}">
        <x-resource.alpine-field label="Originalpreis" x-model="{!! $originalPrice !!}" />
    </template>
</x-resource>
