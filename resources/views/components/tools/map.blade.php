@props([
    'center' => null,
    'zoom' => 13,
    'markers' => [],
])

<div class="w-full mb-5 rounded-lg overflow-hidden shadow-lg">
    @if($center)
        <div
            class="w-full"
            x-data="DynamicMap({ markers: @js($markers), center: @js($center), zoom: @js($zoom) })"
        >
            <div x-ref="map" id="map" class="aspect-video w-full"></div>
        </div>
    @endif
</div>
