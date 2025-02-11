@props([
    'url' => '',
])
<div
    {{ $attributes
            ->merge($getExtraAttributes())
            ->class('')
    }}
>
    <iframe
        x-bind:src="$wire.$get('{{ $getStatePath() }}')"
        style="zoom: {{ $getZoom() }};"
        width="100%"
        height="100%"
        frameborder="0"
        class="w-full h-full rounded"
    ></iframe>
</div>
