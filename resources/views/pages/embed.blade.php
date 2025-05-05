@props([
    'extractorId'
])

<x-layouts.app>
    <script
        src="{{ asset('vendor/wire-elements/wire-extender.js') }}"
        data-livewire-asset-uri="{{ asset('vendor/livewire/livewire.min.js') }}"
    ></script>
    <div>
        <livewire data-component="embedded-extractor" data-params='@json(['extractorId' => $extractorId])'>
    </livewire>
    </div>
</x-layouts.app>
