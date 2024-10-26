@props([
    'url',
])

<div class="w-full mb-5 rounded-lg overflow-hidden shadow-lg">
    <script src="https://open.spotify.com/embed/iframe-api/v1" async></script>

    <script>
        window.onSpotifyIframeApiReady = (IFrameAPI) => {
          const element = document.getElementById('embed-iframe');
          const options = {
              uri: 'spotify:episode:7makk4oTQel546B0PZlDM5'
            };
          const callback = (EmbedController) => {};
          IFrameAPI.createController(element, options, callback);
        };
    </script>

    <iframe
        src="{{ $url }}"
        allow="fullscreen"
        class="w-full h-32 border-none"
        loading="lazy"
    ></iframe>
</div>
