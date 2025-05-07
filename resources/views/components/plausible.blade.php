@if (config('landing.enable-plausible'))
    @php
        $plausibleUrl = config('landing.plausible-url', 'https://plausible.io');
        $domain = config('landing.plausible-domain', request()->getHost());
    @endphp
    <script defer data-domain="{{ $domain }}" src="{{ $plausibleUrl }}/js/script.js"></script>
@endif
