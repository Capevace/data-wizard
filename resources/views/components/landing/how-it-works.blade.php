<div {{ $attributes->class('grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-16 mx-auto') }}>
    <div class="text-center col-span-1 md:col-span-3">
        <div class="font-medium text-dolphin">
            How it works
        </div>
        <div class="pt-2 text-2xl sm:text-3xl font-black">
            Easy to understand, but powerful
        </div>
    </div>
    <div class="w-full">
        <div class="relative max-w-sm">
            <div class="relative mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-warning-400 text-white">
                @svg('heroicon-o-cloud-arrow-up', 'w-12 h-12')
            </div>
            <h3 class="mb-4 text-2xl font-semibold text-gray-700 text-dark">
                1. Upload your documents
            </h3>
            <p class="text-lg leading-relaxed text-body-color text-gray-600">
                Files are added to buckets that can be re-used for multiple LLM runs.
            </p>
        </div>
    </div>
    <div class="w-full">
        <div class="relative max-w-sm">
            <div class="relative mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-primary-400 text-white">
                @svg('bi-robot', 'w-12 h-12')
            </div>
            <h3 class="mb-4 text-2xl font-semibold text-gray-700 text-dark">2. Start the extraction</h3>
            <p class="text-lg leading-relaxed text-body-color text-gray-600">
                Use pre-defined extraction strategies and prompts or create your own.
            </p>
        </div>
    </div>
    <div class="w-full">
        <div class="relative max-w-sm">
            <div class="relative mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-success-400 text-white">
                @svg('heroicon-o-document-text', 'w-12 h-12')
            </div>
            <h3 class="mb-4 text-2xl font-semibold text-gray-700 text-dark">3. Use your validated JSON</h3>
            <p class="text-lg leading-relaxed text-body-color text-gray-600">
                The JSON can be downloaded via APIs. You can also be notified via email or webhooks.
            </p>
        </div>
    </div>
</div>
