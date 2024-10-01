<div {{ $attributes->class('grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16 mx-auto') }}>
    <div class="text-center col-span-1 md:col-span-full">
        <div class="font-medium text-dolphin">
            Data Wizard has iFrames, HTTP API and PHP SDK
        </div>
        <div class="pt-2 text-2xl sm:text-3xl font-black">
            Usable in four main ways
        </div>
    </div>
    <div class="w-full">
        <div class="relative max-w-sm">
            <div class="relative mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-warning-400 text-white">
                @svg('heroicon-o-cloud-arrow-up', 'w-12 h-12')
            </div>
            <h3 class="mb-4 text-2xl font-semibold text-gray-700 text-dark">
                Open link and get a Webhook
            </h3>
            <p class="text-base leading-relaxed text-body-color text-gray-600">
                You can simply redirect your users to extraction pages and they will be redirected back to you after the data has been extracted. You will also receive a webhook with the data.
            </p>
        </div>
    </div>
    <div class="w-full">
        <div class="relative max-w-sm">
            <div class="relative mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-warning-400 text-white">
                @svg('heroicon-o-cloud-arrow-up', 'w-12 h-12')
            </div>
            <h3 class="mb-4 text-2xl font-semibold text-gray-700 text-dark">
                Embed using iFrames
            </h3>
            <p class="text-base leading-relaxed text-body-color text-gray-600">
                You can embed a data wizard directly into your website/form using our iFrame SDK. You will receive a callback using JavaScript when the data has been extracted.
            </p>
        </div>
    </div>

    <div class="w-full">
        <div class="relative max-w-sm">
            <div class="relative mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-success-400 text-white">
                @svg('heroicon-o-document-text', 'w-12 h-12')
            </div>
            <h3 class="mb-4 text-2xl font-semibold text-gray-700 text-dark">Use the standalone application</h3>
            <p class="text-base leading-relaxed text-body-color text-gray-600">
                The app comes with a built-in admin interfaces for managing extractors and uploaded data, which is helpful for one-off scenarios.
            </p>
            <p class="text-base leading-relaxed text-body-color text-gray-600 mb-1">
                You can also oversee your LLM costs as well as inspect extraction runs and their results.
            </p>
        </div>
    </div>

    <div class="w-full">
        <div class="relative max-w-sm">
            <div class="relative mb-6 flex h-20 w-20 items-center justify-center rounded-3xl bg-primary-400 text-white">
                @svg('bi-robot', 'w-12 h-12')
            </div>
            <h3 class="mb-4 text-2xl font-semibold text-gray-700 text-dark">
                Use as PHP SDK
            </h3>
            <p class="text-base leading-relaxed text-body-color text-gray-600 mb-1">
                <a class="font-semibold text-primary-600 hover:text-primary-400" href="#">MagicLLM</a>, the PHP SDK powering MagicExtract, offers a universal API interface for multiple LLMs and providers.
            </p>
            <p class="text-base leading-relaxed text-body-color text-gray-600">
                It also supports features like streaming and function calling, as well es exposing the complex extraction logic via a simple SDK interface.
            </p>
        </div>
    </div>
</div>
