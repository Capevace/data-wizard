@props([
    'url',
    'aspectVideo' => false,
])

<div
    class="w-full mb-5 "
    x-data
    x-init="$refs.iframe.allowTransparency = true"
>
    <iframe
        style="border-radius:12px"
        src="{{ $url }}"
        width="100%"
        @if ($aspectVideo)
            height="100%"
        @else
            height="232"
        @endif
        frameBorder="0"
        allowfullscreen=""
        allow="autoplay; encrypted-media; fullscreen; picture-in-picture"
        loading="lazy"
        class="w-full {{ $aspectVideo ? 'aspect-video' : '' }}"
    ></iframe>
</div>
