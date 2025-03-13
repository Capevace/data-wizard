<section class="bg-gradient-to-r from-indigo-500 to-teal-500 text-white w-full h-10 flex items-center justify-center px-3">
    <a href="https://mateffy.me" class="flex flex-col items-center justify-center text-xs">
        <h3 class="font-semibold -mb-0.5">Developed by Lukas Mateffy</h3>
        <span>mateffy.me</span>
    </a>
    <div></div>
</section>

<div class="max-w-6xl mx-auto px-4 md:px-2 mb-8">
    <header class="max-w-screen-5xl w-full mx-auto px-5 pt-10 min-[550px]:px-10 sm:overflow-x-visible flex items-center justify-between">
        <div class="flex gap-3 items-start justify-start">
            <div class="text-6xl">🪄</div>
            <div>
                <h1 class="text-3xl font-bold leading-10 text-gray-800 sr-only">
                    Data Wizard
                </h1>
                <img
                    src="{{ asset('images/logo-text.svg') }}"
                    alt="Data Wizard logo"
                    class="h-12 -ml-1.5 w-auto"
                    aria-hidden="true"
                >
                <a href="https://mateffy.me">A project by Lukas Mateffy</a>
            </div>
        </div>
        <nav class="flex gap-5 items-center justify-end">
            <x-filament::link
                href="{{ config('app.github_url') }}"
                tag="a"
                target="_blank"
                icon="bi-github"
                icon-position="after"
                 color="gray"
            >
                Source Code
            </x-filament::link>
            <x-filament::link href="{{ config('app.github_url') }}" tag="a" target="_blank" icon="heroicon-o-document-text" icon-position="after"  color="gray">
                Documentation
            </x-filament::link>
            <x-filament::link href="{{ config('app.github_url') }}" tag="a" target="_blank" icon="heroicon-o-academic-cap" icon-position="after"  color="gray">
                Thesis
            </x-filament::link>

            <div class="w-1"></div>

            @auth
                <x-filament::link href="{{ \Filament\Pages\Dashboard::getUrl() }}" tag="a" icon="bi-cloud" icon-position="after"  color="gray">
                    Open app
                </x-filament::link>
            @endauth

            @guest
                <x-filament::link href="{{ route('filament.app.auth.login') }}" tag="a" icon="bi-person-plus" icon-position="after">
                    Sign in or Register
                </x-filament::link>
            @endguest
        </nav>
    </header>
    <div class="md:px-6 lg:px-0">
        <x-landing.header class="mb-20" />


{{--        <x-landing.video class="mb-10" />--}}
{{--        <x-landing.how-it-works />--}}
{{--        <x-landing.what-is-it class="mb-28" />--}}
{{--        <x-landing.usages class="mb-28" />--}}
{{--        <x-landing.easy-usage class="mb-28" />--}}



{{--        <section class="mt-20">--}}
{{--            <div class="max-w-4xl mb-12">--}}
{{--                <h2 class="text-4xl md:text-5xl font-bold text-gray-700">--}}
{{--                    <rough-notation data-stroke-width="4" class="inline">Tailor-made</rough-notation>--}}
{{--                    <svg class="rough-annotation"--}}
{{--                         style="position: absolute; top: 0px; left: 0px; overflow: visible; pointer-events: none; width: 100px; height: 100px;">--}}
{{--                        <path--}}
{{--                            d="M313.10056674720374 2610.1904783231394 C413.00278601041674 2609.9087149550733, 507.1288177415083 2607.482831616256, 578.0845621899688 2607.772151868138"--}}
{{--                            fill="none" stroke="#fbbd23" stroke-width="4" data-darkreader-inline-stroke=""--}}
{{--                            style="--darkreader-inline-stroke: #fbc233;"></path>--}}
{{--                        <path--}}
{{--                            d="M576.330347310124 2609.1054855723783 C508.2780836445598 2613.3023150462923, 441.80630717707646 2611.086347081166, 312.6283342993228 2611.4343766591414"--}}
{{--                            fill="none" stroke="#fbbd23" stroke-width="4" data-darkreader-inline-stroke=""--}}
{{--                            style="--darkreader-inline-stroke: #fbc233;"></path>--}}
{{--                    </svg>--}}
{{--                    for developers--}}
{{--                </h2>--}}
{{--            </div>--}}
{{--            <section class="space-y-6 md:space-y-0 md:flex md:items-start md:space-x-12">--}}
{{--                <div class="w-full space-y-1 md:w-1/2">--}}
{{--                    <carousel-item data-active="false" data-step-id="api_and_webhooks"--}}
{{--                                   class="block p-4 rounded-2xl hover:bg-base-200/70">--}}
{{--                        <a href="#">--}}
{{--                            <div class="flex items-center space-x-2 mb-2">--}}
{{--                                <div--}}
{{--                                    class="text-white text-center inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-400">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" width="44"--}}
{{--                                         height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"--}}
{{--                                         fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                         data-darkreader-inline-stroke=""--}}
{{--                                         style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"--}}
{{--                                              data-darkreader-inline-stroke=""--}}
{{--                                              style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                        <path d="M4.876 13.61a4 4 0 1 0 6.124 3.39h6"></path>--}}
{{--                                        <path--}}
{{--                                            d="M15.066 20.502a4 4 0 1 0 1.934 -7.502c-.706 0 -1.424 .179 -2 .5l-3 -5.5"></path>--}}
{{--                                        <path d="M16 8a4 4 0 1 0 -8 0c0 1.506 .77 2.818 2 3.5l-3 5.5"></path>--}}
{{--                                    </svg>--}}

{{--                                </div>--}}
{{--                                <p class="text-xl font-bold text-gray-700">API and Webhooks</p>--}}
{{--                            </div>--}}
{{--                            <p class="text-gray-600">Utilize the power of our REST API for seamless integration with--}}
{{--                                your application or create simple automation with Zapier.</p>--}}
{{--                            <progress class="progress progress-secondary w-full" value="0" max="100"></progress>--}}
{{--                        </a>--}}
{{--                    </carousel-item>--}}
{{--                    <carousel-item data-active="true" data-step-id="embedded"--}}
{{--                                   class="block p-4 rounded-2xl hover:bg-base-200/70 bg-base-200/70">--}}
{{--                        <a href="#">--}}
{{--                            <div class="flex items-center space-x-2 mb-2">--}}
{{--                                <div--}}
{{--                                    class="text-white text-center inline-flex items-center justify-center w-10 h-10 rounded-full bg-orange-400">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" width="44"--}}
{{--                                         height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"--}}
{{--                                         fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                         data-darkreader-inline-stroke=""--}}
{{--                                         style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"--}}
{{--                                              data-darkreader-inline-stroke=""--}}
{{--                                              style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                        <path d="M7 8l-4 4l4 4"></path>--}}
{{--                                        <path d="M17 8l4 4l-4 4"></path>--}}
{{--                                        <path d="M14 4l-4 16"></path>--}}
{{--                                    </svg>--}}

{{--                                </div>--}}
{{--                                <p class="text-xl font-bold text-gray-700">Embedded</p>--}}
{{--                            </div>--}}
{{--                            <p class="text-gray-600">Use embeddable code snippets to seamlessly implement the--}}
{{--                                document signing workflows directly on your website or app.</p>--}}
{{--                            <progress class="progress progress-secondary w-full" value="54.899999999999835"--}}
{{--                                      max="100"></progress>--}}
{{--                        </a>--}}
{{--                    </carousel-item>--}}
{{--                    <carousel-item data-active="false" data-step-id="html_to_pdf_api"--}}
{{--                                   class="block p-4 rounded-2xl hover:bg-base-200/70">--}}
{{--                        <a href="#">--}}
{{--                            <div class="flex items-center space-x-2 mb-2">--}}
{{--                                <div--}}
{{--                                    class="text-white text-center inline-flex items-center justify-center w-10 h-10 rounded-full bg-warning-400">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" width="44"--}}
{{--                                         height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"--}}
{{--                                         fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--                                         data-darkreader-inline-stroke=""--}}
{{--                                         style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"--}}
{{--                                              data-darkreader-inline-stroke=""--}}
{{--                                              style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>--}}
{{--                                        <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"></path>--}}
{{--                                        <path d="M2 21v-6"></path>--}}
{{--                                        <path d="M5 15v6"></path>--}}
{{--                                        <path d="M2 18h3"></path>--}}
{{--                                        <path d="M20 15v6h2"></path>--}}
{{--                                        <path d="M13 21v-6l2 3l2 -3v6"></path>--}}
{{--                                        <path d="M7.5 15h3"></path>--}}
{{--                                        <path d="M9 15v6"></path>--}}
{{--                                    </svg>--}}

{{--                                </div>--}}
{{--                                <p class="text-xl font-bold text-gray-700">HTML to PDF form</p>--}}
{{--                            </div>--}}
{{--                            <p class="text-gray-600">Build fillable document forms using our pixel-perfect HTML API,--}}
{{--                                reducing the time for creating personalized documents.</p>--}}
{{--                            <progress class="progress progress-secondary w-full" value="0" max="100"></progress>--}}
{{--                        </a>--}}
{{--                    </carousel-item>--}}
{{--                </div>--}}
{{--                <div class="w-full md:w-1/2 flex-shrink-0">--}}
{{--                    <div id="api_and_webhooks" class="md:h-[510px] hidden">--}}
{{--                        <api-snippet class="block mockup-code pb-0 overflow-hidden">--}}
{{--            <span class="top-0 right-0 absolute flex items-center overflow-x-auto">--}}
{{--            </span>--}}
{{--                            <pre class="before:!m-0 pl-6 pb-4 max-h-80 overflow-auto text-sm"><code--}}
{{--                                    data-lang="node_axios"><span--}}
{{--                                        style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                        data-darkreader-inline-color="">import</span> <span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">axios</span> <span--}}
{{--                                        style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                        data-darkreader-inline-color="">from</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">axios</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">;</span>--}}

{{--<span style="color: rgb(210, 132, 69); --darkreader-inline-color: #d68e54;" data-darkreader-inline-color="">const</span> <span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">options</span> <span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">=</span> <span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">{</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">method</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">POST</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">,</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">url</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">https://api.docuseal.co/submissions</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">,</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">headers</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">{</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">X-Auth-Token</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">API_KEY</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">,</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">content-type</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">application/json</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">},</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">data</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">{</span>--}}
{{--    <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;" data-darkreader-inline-color="">template_id</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">1000001</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">,</span>--}}
{{--    <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;" data-darkreader-inline-color="">send_email</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(210, 132, 69); --darkreader-inline-color: #d68e54;"--}}
{{--                                        data-darkreader-inline-color="">true</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">,</span>--}}
{{--    <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;" data-darkreader-inline-color="">submitters</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">[{</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">role</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">First Party</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">,</span> <span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">email</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">:</span> <span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">john.doe@example.com</span><span--}}
{{--                                        style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                        data-darkreader-inline-color="">'</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">}]</span>--}}
{{--  <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">}</span>--}}
{{--<span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">};</span>--}}

{{--<span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--      data-darkreader-inline-color="">axios</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">.</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">request</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">(</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">options</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">).</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">then</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">(</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">function </span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">(</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">response</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">)</span> <span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">{</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">console</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">.</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">log</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">(</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">response</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">.</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">data</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">);</span>--}}
{{--<span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--      data-darkreader-inline-color="">}).</span><span--}}
{{--                                        style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                        data-darkreader-inline-color="">catch</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">(</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">function </span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">(</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">error</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">)</span> <span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">{</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">console</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">.</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">error</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">(</span><span--}}
{{--                                        style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                        data-darkreader-inline-color="">error</span><span--}}
{{--                                        style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                        data-darkreader-inline-color="">);</span>--}}
{{--<span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">});</span></code></pre>--}}
{{--                        </api-snippet>--}}
{{--                        <div class="flex items-center justify-center py-4">--}}
{{--                            <a class="btn btn-primary-500 text-base w-full md:w-64" href="/docs/api">API Reference</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div id="embedded" class="md:h-[510px]">--}}
{{--                        <toggle-visible--}}
{{--                            data-element-ids="[&quot;js&quot;,&quot;react&quot;,&quot;vue&quot;,&quot;angular&quot;]"--}}
{{--                            class="block relative" data-catalyst="">--}}
{{--                            <ul class="items-center w-full text-sm font-medium text-gray-900 mb-4 space-y-2 sm:space-y-0 sm:flex sm:space-x-2">--}}
{{--                                <li class="w-full h-10 text-sm font-medium flex items-center relative group py-3.5">--}}
{{--                                    <input type="radio" name="option" id="js_radio" value="js"--}}
{{--                                           data-action="change:toggle-visible#trigger" class="relative peer z-10 hidden"--}}
{{--                                           checked="checked">--}}
{{--                                    <label for="js_radio"--}}
{{--                                           class="absolute border-gray-focus space-x-2 border rounded-xl left-0 right-0 top-0 bottom-0 flex items-center justify-center group-hover:bg-gray group-hover:text-white peer-checked:btn-gray">--}}
{{--                      <span><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" x="0px" y="0px" width="100"--}}
{{--                                 height="100" viewBox="0 0 48 48">--}}
{{--<path fill="#ffd600" d="M6,42V6h36v36H6z" data-darkreader-inline-fill=""--}}
{{--      style="--darkreader-inline-fill: #ffda1a;"></path><path fill="#000001"--}}
{{--                                                              d="M29.538 32.947c.692 1.124 1.444 2.201 3.037 2.201 1.338 0 2.04-.665 2.04-1.585 0-1.101-.726-1.492-2.198-2.133l-.807-.344c-2.329-.988-3.878-2.226-3.878-4.841 0-2.41 1.845-4.244 4.728-4.244 2.053 0 3.528.711 4.592 2.573l-2.514 1.607c-.553-.988-1.151-1.377-2.078-1.377-.946 0-1.545.597-1.545 1.377 0 .964.6 1.354 1.985 1.951l.807.344C36.452 29.645 38 30.839 38 33.523 38 36.415 35.716 38 32.65 38c-2.999 0-4.702-1.505-5.65-3.368L29.538 32.947zM17.952 33.029c.506.906 1.275 1.603 2.381 1.603 1.058 0 1.667-.418 1.667-2.043V22h3.333v11.101c0 3.367-1.953 4.899-4.805 4.899-2.577 0-4.437-1.746-5.195-3.368L17.952 33.029z"--}}
{{--                                                              data-darkreader-inline-fill=""--}}
{{--                                                              style="--darkreader-inline-fill: #e8e6e3;"></path>--}}
{{--</svg>--}}
{{--</span>--}}
{{--                                        <span>JavaScript</span>--}}
{{--                                    </label>--}}
{{--                                </li>--}}
{{--                                <li class="w-full h-10 text-sm font-medium flex items-center relative group py-3.5">--}}
{{--                                    <input type="radio" name="option" id="react_radio" value="react"--}}
{{--                                           data-action="change:toggle-visible#trigger"--}}
{{--                                           class="relative peer z-10 hidden">--}}
{{--                                    <label for="react_radio"--}}
{{--                                           class="absolute border-gray-focus space-x-2 border rounded-xl left-0 right-0 top-0 bottom-0 flex items-center justify-center group-hover:bg-gray group-hover:text-white peer-checked:btn-gray">--}}
{{--                      <span><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"--}}
{{--                                 viewBox="-11.5 -10.23174 23 20.46348">--}}
{{--  <circle cx="0" cy="0" r="2.05" fill="#61dafb" data-darkreader-inline-fill=""--}}
{{--          style="--darkreader-inline-fill: #5fd9fb;"></circle>--}}
{{--  <g stroke="#61dafb" stroke-width="1" fill="none" data-darkreader-inline-stroke=""--}}
{{--     style="--darkreader-inline-stroke: #5fd9fb;">--}}
{{--    <ellipse rx="11" ry="4.2"></ellipse>--}}
{{--    <ellipse rx="11" ry="4.2" transform="rotate(60)"></ellipse>--}}
{{--    <ellipse rx="11" ry="4.2" transform="rotate(120)"></ellipse>--}}
{{--  </g>--}}
{{--</svg>--}}
{{--</span>--}}
{{--                                        <span>React</span>--}}
{{--                                    </label>--}}
{{--                                </li>--}}
{{--                                <li class="w-full h-10 text-sm font-medium flex items-center relative group py-3.5">--}}
{{--                                    <input type="radio" name="option" id="vue_radio" value="vue"--}}
{{--                                           data-action="change:toggle-visible#trigger"--}}
{{--                                           class="relative peer z-10 hidden">--}}
{{--                                    <label for="vue_radio"--}}
{{--                                           class="absolute border-gray-focus space-x-2 border rounded-xl left-0 right-0 top-0 bottom-0 flex items-center justify-center group-hover:bg-gray group-hover:text-white peer-checked:btn-gray">--}}
{{--                      <span><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" version="1.1"--}}
{{--                                 viewBox="0 0 261.76 226.69"><g transform="matrix(1.3333 0 0 -1.3333 -76.311 313.34)"><g--}}
{{--                                      transform="translate(178.06 235.01)"><path--}}
{{--                                          d="m0 0-22.669-39.264-22.669 39.264h-75.491l98.16-170.02 98.16 170.02z"--}}
{{--                                          fill="#41b883" data-darkreader-inline-fill=""--}}
{{--                                          style="--darkreader-inline-fill: #59c494;"></path></g><g--}}
{{--                                      transform="translate(178.06 235.01)"><path--}}
{{--                                          d="m0 0-22.669-39.264-22.669 39.264h-36.227l58.896-102.01 58.896 102.01z"--}}
{{--                                          fill="#34495e" data-darkreader-inline-fill=""--}}
{{--                                          style="--darkreader-inline-fill: #a6bcce;"></path></g></g></svg>--}}
{{--</span>--}}
{{--                                        <span>Vue</span>--}}
{{--                                    </label>--}}
{{--                                </li>--}}
{{--                                <li class="w-full h-10 text-sm font-medium flex items-center relative group py-3.5">--}}
{{--                                    <input type="radio" name="option" id="angular_radio" value="angular"--}}
{{--                                           data-action="change:toggle-visible#trigger"--}}
{{--                                           class="relative peer z-10 hidden">--}}
{{--                                    <label for="angular_radio"--}}
{{--                                           class="absolute border-gray-focus space-x-2 border rounded-xl left-0 right-0 top-0 bottom-0 flex items-center justify-center group-hover:bg-gray group-hover:text-white peer-checked:btn-gray">--}}
{{--                      <span><svg class="h-5 w-5" width="800px" height="800px" viewBox="-8 0 272 272" version="1.1"--}}
{{--                                 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"--}}
{{--                                 preserveAspectRatio="xMidYMid">--}}
{{--  <g>--}}
{{--    <path--}}
{{--        d="M0.0996108949,45.522179 L125.908171,0.697276265 L255.103502,44.7252918 L234.185214,211.175097 L125.908171,271.140856 L19.3245136,211.971984 L0.0996108949,45.522179 Z"--}}
{{--        fill="#E23237" data-darkreader-inline-fill="" style="--darkreader-inline-fill: #e44347;">--}}
{{--    </path>--}}
{{--    <path--}}
{{--        d="M255.103502,44.7252918 L125.908171,0.697276265 L125.908171,271.140856 L234.185214,211.274708 L255.103502,44.7252918 L255.103502,44.7252918 Z"--}}
{{--        fill="#B52E31" data-darkreader-inline-fill="" style="--darkreader-inline-fill: #d4585a;">--}}
{{--    </path>--}}
{{--    <path--}}
{{--        d="M126.107393,32.27393 L126.107393,32.27393 L47.7136187,206.692607 L76.9992218,206.194553 L92.7377432,166.848249 L126.207004,166.848249 L126.306615,166.848249 L163.063035,166.848249 L180.29572,206.692607 L208.286381,207.190661 L126.107393,32.27393 L126.107393,32.27393 Z M126.306615,88.155642 L152.803113,143.5393 L127.402335,143.5393 L126.107393,143.5393 L102.997665,143.5393 L126.306615,88.155642 L126.306615,88.155642 Z"--}}
{{--        fill="#FFFFFF" data-darkreader-inline-fill="" style="--darkreader-inline-fill: #e8e6e3;">--}}
{{--    </path>--}}
{{--  </g>--}}
{{--</svg>--}}
{{--</span>--}}
{{--                                        <span>Angular</span>--}}
{{--                                    </label>--}}
{{--                                </li>--}}
{{--                            </ul>--}}
{{--                        </toggle-visible>--}}
{{--                        <disable-hidden id="js" class="block my-4 ">--}}
{{--                            <div class="mockup-code overflow-hidden pb-0 mt-4">--}}
{{--                <span class="top-0 right-0 absolute">--}}


{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 max-h-72 overflow-y-auto"><code--}}
{{--                                        class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&lt;script </span><span--}}
{{--                                            style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                            data-darkreader-inline-color="">src=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"https://cdn.docuseal.co/js/form.js"</span><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&gt;&lt;/script&gt;</span>--}}

{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;docuseal-form</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">id=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"docusealForm"</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">data-src=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"https://docuseal.co/d/LEVGR9rhZYf86M"</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;" data-darkreader-inline-color="">data-email=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"signer@example.com"</span><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&gt;</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/docuseal-form&gt;</span>--}}

{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;script&gt;</span>--}}
{{--  <span--}}
{{--      style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--      data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">window</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">.</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">docusealForm</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">.</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">addEventListener</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">(</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">completed</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">,</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">(</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">e</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">)</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">=&gt;</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">e</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">.</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">detail</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">)</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/script&gt;</span>--}}
{{--</code></pre>--}}
{{--                            </div>--}}
{{--                        </disable-hidden>--}}
{{--                        <disable-hidden id="react" class="block my-4 hidden">--}}
{{--                            <div class="mockup-code overflow-hidden pb-0 mt-4">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="import React from &quot;react&quot;--}}
{{--import { DocusealForm } from '@docuseal/react'--}}

{{--export function App() {--}}
{{--  return (--}}
{{--    <div className=&quot;app&quot;>--}}
{{--      <DocusealForm--}}
{{--        src=&quot;https://docuseal.co/d/LEVGR9rhZYf86M&quot;--}}
{{--        email=&quot;signer@example.com&quot;--}}
{{--        onComplete={(data) => console.log(data)}--}}
{{--      />--}}
{{--    </div>--}}
{{--  );--}}
{{--}--}}
{{--">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 max-h-72 overflow-y-auto"><code--}}
{{--                                        class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                            data-darkreader-inline-color="">import</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">React</span> <span--}}
{{--                                            style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                            data-darkreader-inline-color="">from</span> <span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">react</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"</span>--}}
{{--<span style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--      data-darkreader-inline-color="">import</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">DocusealForm</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">}</span> <span--}}
{{--                                            style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                            data-darkreader-inline-color="">from</span> <span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">@docuseal/react</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span>--}}

{{--<span style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--      data-darkreader-inline-color="">export</span> <span--}}
{{--                                            style="color: rgb(210, 132, 69); --darkreader-inline-color: #d68e54;"--}}
{{--                                            data-darkreader-inline-color="">function</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">App</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">()</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span>--}}
{{--  <span style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--        data-darkreader-inline-color="">return </span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">(</span>--}}
{{--    <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--          data-darkreader-inline-color="">&lt;</span><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">div</span> <span--}}
{{--                                            style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                            data-darkreader-inline-color="">className</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"app"</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">&gt;</span>--}}
{{--      <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--            data-darkreader-inline-color="">&lt;</span><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">DocusealForm</span>--}}
{{--        <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--              data-darkreader-inline-color="">src</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"https://docuseal.co/d/LEVGR9rhZYf86M"</span>--}}
{{--        <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;" data-darkreader-inline-color="">email</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"signer@example.com"</span>--}}
{{--        <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;" data-darkreader-inline-color="">onComplete</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">=</span><span--}}
{{--                                            style="color: rgb(143, 85, 54); --darkreader-inline-color: #cb9476;"--}}
{{--                                            data-darkreader-inline-color="">{</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">(</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">data</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">)</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">=&gt;</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">console</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">.</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">log</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">(</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">data</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">)</span><span--}}
{{--                                            style="color: rgb(143, 85, 54); --darkreader-inline-color: #cb9476;"--}}
{{--                                            data-darkreader-inline-color="">}</span>--}}
{{--      <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--            data-darkreader-inline-color="">/&gt;</span>--}}
{{--    <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--          data-darkreader-inline-color="">&lt;/</span><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">div</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">&gt;</span>--}}
{{--  <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">);</span>--}}
{{--<span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">}</span>--}}
{{--</code></pre>--}}
{{--                            </div>--}}
{{--                        </disable-hidden>--}}
{{--                        <disable-hidden id="vue" class="block my-4 hidden">--}}
{{--                            <div class="mockup-code overflow-hidden pb-0 mt-4">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="<template>--}}
{{--  <DocusealForm--}}
{{--    :src=&quot;'https://docuseal.co/d/LEVGR9rhZYf86M'&quot;--}}
{{--    :email=&quot;'signer@example.com'&quot;--}}
{{--    @complete=&quot;onFormComplete&quot;--}}
{{--  />--}}
{{--</template>--}}

{{--<script>--}}
{{--import { DocusealForm } from '@docuseal/vue'--}}

{{--export default {--}}
{{--  name: 'App',--}}
{{--  components: {--}}
{{--    DocusealForm--}}
{{--  },--}}
{{--  methods: {--}}
{{--    onFormComplete (data) {--}}
{{--      console.log(data)--}}
{{--    }--}}
{{--  }--}}
{{--}--}}
{{--</script>--}}
{{--">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 max-h-72 overflow-y-auto"><code--}}
{{--                                        class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&lt;template&gt;</span>--}}
{{--  <span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;DocusealForm</span>--}}
{{--    <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--          data-darkreader-inline-color="">:src=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"'https://docuseal.co/d/LEVGR9rhZYf86M'"</span>--}}
{{--    <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--          data-darkreader-inline-color="">:email=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"'signer@example.com'"</span>--}}
{{--    <span--}}
{{--        style="color: rgb(21, 21, 21); background-color: rgb(172, 65, 66); --darkreader-inline-color: #dbd8d3; --darkreader-inline-bgcolor: #8a3435;"--}}
{{--        data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">@</span><span--}}
{{--                                            style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--                                            data-darkreader-inline-color="">complete=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"onFormComplete"</span>--}}
{{--  <span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--        data-darkreader-inline-color="">/&gt;</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/template&gt;</span>--}}

{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;script&gt;</span>--}}
{{--<span style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--      data-darkreader-inline-color="">import</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">DocusealForm</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">}</span> <span--}}
{{--                                            style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                            data-darkreader-inline-color="">from</span> <span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">@docuseal/vue</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span>--}}

{{--<span style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--      data-darkreader-inline-color="">export</span> <span--}}
{{--                                            style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                            data-darkreader-inline-color="">default</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">name</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">:</span> <span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">App</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">,</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;" data-darkreader-inline-color="">components</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">:</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span>--}}
{{--    <span--}}
{{--        style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--        data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">DocusealForm</span>--}}
{{--  <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">},</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">methods</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">:</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span>--}}
{{--    <span--}}
{{--        style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--        data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">onFormComplete </span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">(</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">data</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">)</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span>--}}
{{--      <span--}}
{{--          style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--          data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">console</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">.</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">log</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">(</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">data</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">)</span>--}}
{{--    <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--          data-darkreader-inline-color="">}</span>--}}
{{--  <span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">}</span>--}}
{{--<span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">}</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/script&gt;</span>--}}
{{--</code></pre>--}}
{{--                            </div>--}}
{{--                        </disable-hidden>--}}
{{--                        <disable-hidden id="angular" class="block my-4 hidden">--}}
{{--                            <div class="mockup-code overflow-hidden pb-0 mt-4">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="asdasd">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 max-h-72 overflow-y-auto"><code--}}
{{--                                        class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                            data-darkreader-inline-color="">import</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">Component</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">}</span> <span--}}
{{--                                            style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                            data-darkreader-inline-color="">from</span> <span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">@angular/core</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">;</span>--}}
{{--<span style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--      data-darkreader-inline-color="">import</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">DocusealFormComponent</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">}</span> <span--}}
{{--                                            style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--                                            data-darkreader-inline-color="">from</span> <span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">@docuseal/angular</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">;</span>--}}

{{--<span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--      data-darkreader-inline-color="">@</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color=""--}}
{{--                                            data-darkreader-inline-bgcolor="">Component</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">({</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">selector</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">:</span> <span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">app-root</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">'</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">,</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;" data-darkreader-inline-color="">standalone</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">:</span> <span--}}
{{--                                            style="color: rgb(210, 132, 69); --darkreader-inline-color: #d68e54;"--}}
{{--                                            data-darkreader-inline-color="">true</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">,</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">imports</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">:</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">[</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); background-color: rgb(21, 21, 21); --darkreader-inline-color: #cac6bf; --darkreader-inline-bgcolor: #101112;"--}}
{{--                                            data-darkreader-inline-color="" data-darkreader-inline-bgcolor="">DocusealFormComponent</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">],</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">template</span><span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">:</span> <span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">`--}}
{{--    &lt;div class="app"&gt;--}}
{{--      &lt;docuseal-form--}}
{{--        [src]="'https://docuseal.co/d/LEVGR9rhZYf86M'"--}}
{{--        [email]="'signer@example.com'"&gt;--}}
{{--      &lt;/docuseal-form&gt;--}}
{{--    &lt;/div&gt;--}}
{{--  `</span>--}}
{{--<span style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;" data-darkreader-inline-color="">})</span>--}}
{{--<span style="color: rgb(170, 117, 159); --darkreader-inline-color: #a2998d;"--}}
{{--      data-darkreader-inline-color="">export</span> <span--}}
{{--                                            style="color: rgb(210, 132, 69); --darkreader-inline-color: #d68e54;"--}}
{{--                                            data-darkreader-inline-color="">class</span> <span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">AppComponent</span> <span--}}
{{--                                            style="color: rgb(208, 208, 208); --darkreader-inline-color: #cac6bf;"--}}
{{--                                            data-darkreader-inline-color="">{}</span>--}}
{{--</code></pre>--}}
{{--                            </div>--}}
{{--                        </disable-hidden>--}}
{{--                        <div class="flex items-center justify-center py-2">--}}
{{--                            <a class="btn btn-primary-500 text-base w-full md:w-64" href="/docs/embedded/form">Read--}}
{{--                                Docs</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div id="html_to_pdf_api" class="hidden md:h-[510px]">--}}
{{--                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">--}}
{{--                            <div class="mockup-code pb-0 overflow-hidden" style="min-width: 12rem">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="<text-field>--}}
{{--</text-field>">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 w-72"><code class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&lt;text-field&gt;</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/text-field&gt;</span></code></pre>--}}
{{--                            </div>--}}
{{--                            <div class="mockup-code pb-0 overflow-hidden" style="min-width: 12rem">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="<date-field>--}}
{{--</date-field>">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 w-72"><code class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&lt;date-field&gt;</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/date-field&gt;</span></code></pre>--}}
{{--                            </div>--}}
{{--                            <div class="mockup-code pb-0 overflow-hidden" style="min-width: 12rem">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="<select-field--}}
{{--  options=&quot;opt1,opt2&quot;>--}}
{{--</select-field>">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 w-72"><code class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&lt;select-field</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">options=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"opt1,opt2"</span><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&gt;</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/select-field&gt;</span></code></pre>--}}
{{--                            </div>--}}
{{--                            <div class="mockup-code pb-0 overflow-hidden" style="min-width: 12rem">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="<checkbox-field--}}
{{--  role=&quot;Client&quot;>--}}
{{--</checkbox-field>">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 w-72"><code class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&lt;checkbox-field</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">role=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"Client"</span><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&gt;</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/checkbox-field&gt;</span></code></pre>--}}
{{--                            </div>--}}
{{--                            <div class="mockup-code pb-0 overflow-hidden" style="min-width: 12rem">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="<image-field--}}
{{--  name=&quot;Photo&quot;>--}}
{{--</image-field>">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 w-72"><code class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&lt;image-field</span>--}}
{{--  <span style="color: rgb(106, 159, 181); --darkreader-inline-color: #75a6ba;"--}}
{{--        data-darkreader-inline-color="">name=</span><span--}}
{{--                                            style="color: rgb(144, 169, 89); --darkreader-inline-color: #9bb169;"--}}
{{--                                            data-darkreader-inline-color="">"Photo"</span><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&gt;</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/image-field&gt;</span></code></pre>--}}
{{--                            </div>--}}
{{--                            <div class="mockup-code pb-0 overflow-hidden" style="min-width: 12rem">--}}
{{--                <span class="top-0 right-0 absolute">--}}
{{--                  <clipboard-copy data-text="<signature-field>--}}
{{--</signature-field>">--}}
{{--  <label class="btn btn-ghost text-white">--}}
{{--    <input type="radio" class="peer hidden">--}}
{{--    <span class="peer-checked:hidden flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copy--}}
{{--      </span>--}}
{{--    </span>--}}
{{--    <span class="hidden peer-checked:flex items-center space-x-2">--}}
{{--      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" width="44" height="44" viewBox="0 0 24 24"--}}
{{--           stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"--}}
{{--           data-darkreader-inline-stroke="" style="--darkreader-inline-stroke: currentColor;">--}}
{{--  <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--        style="--darkreader-inline-stroke: none;"></path>--}}
{{--  <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>--}}
{{--  <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>--}}
{{--  <path d="M9 14l2 2l4 -4"></path>--}}
{{--</svg>--}}

{{--      <span class="hidden md:inline">--}}
{{--        Copied--}}
{{--      </span>--}}
{{--    </span>--}}
{{--  </label>--}}
{{--</clipboard-copy>--}}

{{--                </span>--}}
{{--                                <pre class="before:!m-0 pl-6 pb-4 w-72"><code class="overflow-hidden w-full"><span--}}
{{--                                            style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;"--}}
{{--                                            data-darkreader-inline-color="">&lt;signature-field&gt;</span>--}}
{{--<span style="color: rgb(244, 191, 117); --darkreader-inline-color: #f4bc6f;" data-darkreader-inline-color="">&lt;/signature-field&gt;</span></code></pre>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="flex items-center justify-center py-4">--}}
{{--                            <a class="btn btn-primary-500 text-base w-full md:w-64"--}}
{{--                               href="/blog/creating-fillable-pdf-document-forms-with-html">Read Guide</a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </section>--}}
{{--        </section>--}}
{{--        <section class="mt-12">--}}
{{--            <div class="mx-auto mb-12 max-w-4xl text-center">--}}
{{--                <h2 class="text-4xl font-bold text-dark sm:text-4xl md:text-5xl text-gray-700">--}}
{{--                    Frequently asked questions--}}
{{--                </h2>--}}
{{--            </div>--}}
{{--            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                Are DocuSeal signatures legally binding?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        Yes, DocuSeal aligns with the ESIGN Act and the UETA in the US, and the eIDAS regulation in--}}
{{--                        Europe (first level), making signed documents legally binding. Visit our <a class="link"--}}
{{--                                                                                                    href="/compliance">compliance</a>--}}
{{--                        page to learn more.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                How do I send a signing invite from my email address?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        DocuSeal Pro users can connect their Gmail or Outlook accounts to send all signing invitation--}}
{{--                        emails from their connected email address under a custom domain. All email messages sent via--}}
{{--                        DocuSeal are customizable.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                Can I use my own branding?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        Go to <span class="font-bold">Settings</span> and select <span--}}
{{--                            class="font-bold">Personalization</span> from the list of menu items on the left side.--}}
{{--                        Then click <span class="font-bold">Upload Logo</span> to upload an image.--}}
{{--                        Your company logo will be used in emails and on the document signing form.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                Can I use DocuSeal on my mobile device?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        Yes. With our step-by-step form users have a smooth signing experience on small mobile screens.--}}
{{--                        Also, you can upload documents and request signatures directly from your device!--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                How do I add a signing form into my website or mobile app?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        Utilize our no-code solution to effortlessly integrate a document signing form into any web or--}}
{{--                        mobile app.--}}
{{--                        Visit our <a class="link" href="/embedding">embedding</a> page to learn more about how to add--}}
{{--                        document signing into your website or app.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                How to add a signer phone verification via SMS?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        Click on <span class="font-bold">Add Recipients</span> to open a modal form and select to invite--}}
{{--                        recipients <span class="font-bold">via phone</span>. Enter the phone number and name (optional).--}}
{{--                        This feature is a part of the DocuSeal <a href="/pricing" class="link">Pro</a> plan.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                Is DocuSeal GDPR compliant?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        <a href="https://docuseal.eu/sign_up" target="_blank" rel="noopener noreferrer nofollow"--}}
{{--                           class="link">DocuSeal.eu</a> Cloud is GDPR-ready with servers located in Dublin, Ireland.--}}
{{--                        Also, our <a href="/on-premises" class="link">on-premises</a> offering allows you to store your--}}
{{--                        data on your own servers to get the control needed for GDPR compliance.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                Is DocuSeal HIPAA compliant?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        Yes, DocuSeal has implemented all HIPAA requirements and has been reviewed by a third-party--}}
{{--                        compliance provider. <a class="link" href="/hipaa">Sign a BAA</a> to enter our HIPAA Cloud or--}}
{{--                        use <a href="/on-premises" class="link">on-premises</a> to store your patients data.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <div class="h-14">--}}
{{--                        <div class="flex items-center space-x-2">--}}
{{--                            <div class="text-warning-400">--}}
{{--                                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" width="44" height="44"--}}
{{--                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"--}}
{{--                                     stroke-linecap="round" stroke-linejoin="round" data-darkreader-inline-stroke=""--}}
{{--                                     style="--darkreader-inline-stroke: currentColor;">--}}
{{--                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" data-darkreader-inline-stroke=""--}}
{{--                                          style="--darkreader-inline-stroke: none;"></path>--}}
{{--                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z"></path>--}}
{{--                                    <path d="M12 16v.01"></path>--}}
{{--                                    <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>--}}
{{--                                </svg>--}}

{{--                            </div>--}}
{{--                            <p class="text-lg font-semibold text-gray-700 leading-6">--}}
{{--                                How do I contact sales?--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-600">--}}
{{--                        You can email us at <a href="mailto:sales@docuseal.co" class="link">sales@docuseal.co</a> or--}}
{{--                        book a meeting with the team using this <a href="/contact" class="link">link</a>.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--        </section>--}}
{{--        <section>--}}
{{--            <div class="py-12">--}}
{{--                <h2 class="text-4xl font-bold text-gray-700 lg:text-5xl text-center pb-10">--}}
{{--                    Get started now--}}
{{--                </h2>--}}
{{--                <div class="mx-auto max-w-5xl bg-base-200/50 rounded-3xl px-6 py-8 md:px-12 md:py-12">--}}
{{--                    <div class="flex flex-col md:flex-row">--}}
{{--                        <div class="flex w-full flex-col space-y-3 md:space-y-0 justify-between items-center">--}}
{{--                            <div class="mb-8">--}}
{{--                                <div class="mx-auto">--}}
{{--                                    <p class="text-3xl font-bold tracking-tight text-center text-gray-700 sm:text-4xl">--}}
{{--                                        Sign for free<br>--}}
{{--                                        <span class="text-primary-500 tracking-wider">in the Cloud</span></p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="my-auto flex">--}}
{{--                                <a class="btn btn-base btn-outline btn-lg w-72" data-role="app-link"--}}
{{--                                   data-event="Click Sign Up"--}}
{{--                                   data-event-props="{&quot;location&quot;:&quot;Bottom of the landing page&quot;}"--}}
{{--                                   href="https://docuseal.co/sign_up" rel="noopener noreferrer nofollow"--}}
{{--                                   data-event-initialized="true">--}}
{{--                                    <svg version="1.1" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg"--}}
{{--                                         xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"--}}
{{--                                         viewBox="0 0 128 128" style="enable-background:new 0 0 128 128;"--}}
{{--                                         xml:space="preserve">--}}
{{--<polygon style="fill: rgb(202, 44, 49); --darkreader-inline-fill: #d7484c;"--}}
{{--         points="3.77,71.73 20.11,55.63 47.93,50.7 45.18,65.26 7.57,76.82 5.14,75.77 "--}}
{{--         data-darkreader-inline-fill=""></polygon>--}}
{{--                                        <polygon style="fill: rgb(160, 36, 34); --darkreader-inline-fill: #de6765;"--}}
{{--                                                 points="22.94,59.76 5.2,75.88 18.25,82.24 38.06,72.13 38.06,67.36 42.11,56.44 "--}}
{{--                                                 data-darkreader-inline-fill=""></polygon>--}}
{{--                                        <path style="fill: rgb(160, 36, 34); --darkreader-inline-fill: #de6765;" d="M64.92,88.15l-8.57,3.72l-8.09,17.15c0,0,7.12,15.77,7.44,15.77c0.32,0,4.37,0.32,4.37,0.32--}}
{{--	l14.4-16.1l3.64-27.5L64.92,88.15z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(202, 44, 49); --darkreader-inline-fill: #d7484c;" d="M56.5,100.84c0,0,4.77-0.97,8.17-2.59c3.4-1.62,7.6-4.04,7.6-4.04l-1.54,13.43l-15.05,17.13--}}
{{--	c0,0-0.59-0.73-3.09-6.17c-1.99-4.34-2.68-5.89-2.68-5.89L56.5,100.84z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(247, 215, 77); --darkreader-inline-fill: #f7d852;" d="M31.58,80.66c0,0-5.74-0.48-12.03,7.47c-5.74,7.26-8.43,19.08-9.47,22.12s-3.53,3.66-2.7,5.05--}}
{{--	s4.42,1.31,8.85,0.76s8.23-1.94,8.23-1.94s-0.19,0.48-0.83,1.52c-0.23,0.37-1.03,0.9-0.97,1.45c0.14,1.31,11.36,1.34,20.32-7.88--}}
{{--	c9.68-9.95,4.98-18.11,4.98-18.11L31.58,80.66z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(251, 240, 180); --darkreader-inline-fill: #faeb9a;" d="M33.31,85.29c0,0-6.19,0.33-11.31,8.28s-7.5,17.16-7.01,17.78c0.48,0.62,10.02-2.83,12.31-2.14--}}
{{--	c1.57,0.48,0.76,2.07,1.18,2.49c0.35,0.35,4.49,0.94,11.19-6.32c6.71-7.26,5.12-17.46,5.12-17.46L33.31,85.29z"--}}
{{--                                              data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(133, 133, 133); --darkreader-inline-fill: #9b9285;" d="M36.35,74.44c0,0-3.11,2.77-4.22,4.36c-1.11,1.59-1.11,1.73-1.04,2.21--}}
{{--	c0.07,0.48,1.22,5.75,6.01,10.37c5.88,5.67,11.13,6.43,11.89,6.43c0.76,0,5.81-5.67,5.81-5.67L36.35,74.44z"--}}
{{--                                              data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(67, 118, 135); --darkreader-inline-fill: #7eafbf;" d="M50.1,91.24c0,0,5.04,3.31,13.49,0.47c11.55-3.88,20.02-12.56,30.51-23.52--}}
{{--	c10.12-10.58,18.61-23.71,18.61-23.71l-5.95-19.93L50.1,91.24z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(63, 84, 95); --darkreader-inline-fill: #b6b0a6;" d="M67.99,80.33l1.39-4.32l3.48,0.49c0,0,2.65,1.25,4.6,2.16c1.95,0.91,4.46,1.6,4.46,1.6l-4.95,4.18--}}
{{--	c0,0-2.7-1.02-4.67-1.88C70.08,81.59,67.99,80.33,67.99,80.33z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(141, 175, 191); --darkreader-inline-fill: #8fb0c0;" d="M84.32,16.14c0,0-9.62,5.58-23.41,18.63c-12.43,11.76-21.64,22.4-23.87,31.45--}}
{{--	c-1.86,7.58-0.87,12.18,3.36,17.15c4.47,5.26,9.71,7.87,9.71,7.87s3.94,0.06,20.38-12.59c20.51-15.79,36.94-42.23,36.94-42.23--}}
{{--	L84.32,16.14z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(216, 63, 34); --darkreader-inline-fill: #e0563c;" d="M104.18,41.84c0,0-8.37-3.57-14.34-11.9c-5.93-8.27-5.46-13.86-5.46-13.86s4.96-3.89,16.11-8.34--}}
{{--	c7.5-2.99,17.71-4.52,21.07-2.03s-2.3,14.98-2.3,14.98l-10.31,19.96L104.18,41.84z"--}}
{{--                                              data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(104, 150, 165); --darkreader-inline-fill: #759fad;" d="M68.17,80.4c0,0-7.23-3.69-11.83-8.94c-8.7-9.91-10.5-20.79-10.5-20.79l4.37-5.13--}}
{{--	c0,0,1.09,11.56,10.42,21.55c6.08,6.51,12.43,9.49,12.43,9.49s-1.27,1.07-2.63,2.11C69.56,79.36,68.17,80.4,68.17,80.4z"--}}
{{--                                              data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(160, 36, 34); --darkreader-inline-fill: #de6765;" d="M112.71,44.48c0,0,4.34-5.23,8.45-17.02c5.74-16.44,0.74-21.42,0.74-21.42s-1.69,7.82-7.56,18.69--}}
{{--	c-4.71,8.71-10.41,17-10.41,17s3.14,1.41,4.84,1.9C110.91,44.25,112.71,44.48,112.71,44.48z"--}}
{{--                                              data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(179, 225, 238); --darkreader-inline-fill: #a0daea;" d="M39.81,69.66c1.3,1.24,3.27-0.06,4.56-3.1c1.3-3.04,1.28-4.74,0.28-5.46--}}
{{--	c-1.24-0.9-3.32,1.07-4.23,2.82C39.42,65.86,38.83,68.72,39.81,69.66z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(179, 225, 238); --darkreader-inline-fill: #a0daea;" d="M84.95,20.13c0,0-7.61,5.47-15.73,12.91c-7.45,6.83-12.39,12.17-13.07,13.41--}}
{{--	c-0.72,1.33-0.73,3.21-0.17,4.17s1.8,1.46,2.93,0.62c1.13-0.85,9.18-9.75,16.45-16.11c6.65-5.82,11.78-9.51,11.78-9.51--}}
{{--	s2.08-3.68,1.74-4.52C88.54,20.25,84.95,20.13,84.95,20.13z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(237, 106, 101); --darkreader-inline-fill: #ed6b66;" d="M84.95,20.13c0,0,5.62-4.31,11.74-7.34c5.69-2.82,11.35-5.17,12.37-3.13--}}
{{--	c0.97,1.94-5.37,4.58-10.95,8.14c-5.58,3.56-10.95,7.81-10.95,7.81s-0.82-1.5-1.35-2.89C85.22,21.21,84.95,20.13,84.95,20.13z"--}}
{{--                                              data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(225, 225, 225); --darkreader-inline-fill: #d5d1cc;" d="M89.59,39.25c-5.57-5.13-13.32-3.75-17.14,0.81c-3.92,4.7-3.63,11.88,1,16.2--}}
{{--	c4.21,3.92,12.04,4.81,16.76-0.69C94.41,50.69,94.15,43.44,89.59,39.25z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(63, 84, 95); --darkreader-inline-fill: #b6b0a6;" d="M75.33,41.87c-3.31,3.25-3.13,9.69,0.81,12.63c3.44,2.57,8.32,2.44,11.38-0.69--}}
{{--	c3.06-3.13,3.06-8.82,0.19-11.76C84.41,38.68,79.12,38.15,75.33,41.87z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(160, 37, 36); --darkreader-inline-fill: #dc6665;" d="M50,76.89c0,0,6.19-6.28,6.87-5.6c0.68,0.68,0.59,4.49-2.37,8.73c-2.97,4.24-9.5,11.79-14.67,16.88--}}
{{--	c-5.1,5.01-12.29,10.74-12.97,10.64c-0.53-0.08-2.68-1.15-3.54-2.19c-0.84-1.03,1.67-5.9,2.68-7.51C27.02,96.23,50,76.89,50,76.89z"--}}
{{--                                              data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(202, 44, 49); --darkreader-inline-fill: #d7484c;" d="M21.23,101.85c-0.08,1.44,2.12,3.54,2.12,3.54L56.87,71.3c0,0-1.57-1.77-6.19,1.1--}}
{{--	c-4.66,2.9-8.74,6.38-14.76,12.21C27.53,92.75,21.31,100.41,21.23,101.85z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(255, 255, 255); --darkreader-inline-fill: #e8e6e3;" d="M19.06,36.95c-1.11,1.11-1.16,2.89,0.08,3.91c1.1,0.91,2.89,0.32,3.56-0.5s0.59-2.6-0.3-3.48--}}
{{--	C21.51,35.99,19.74,36.28,19.06,36.95z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path--}}
{{--                                            style="opacity: 0.5; fill: rgb(255, 255, 255); --darkreader-inline-fill: #e8e6e3;"--}}
{{--                                            d="M41.02,35.65c-0.84,0.93-0.57,2.31,0.21,2.82s1.95,0.46,2.52-0.24--}}
{{--	c0.51-0.63,0.57-1.89-0.21-2.67C42.86,34.89,41.56,35.05,41.02,35.65z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(255, 255, 255); --darkreader-inline-fill: #e8e6e3;" d="M55.55,11.89c0,0,1.22-3.48,1.94-3.52c0.73-0.04,1.78,3.48,1.78,3.48s3.61,0.04,3.85,0.57--}}
{{--	c0.31,0.68-2.31,2.96-2.31,2.96s0.85,3.4,0.45,3.81c-0.45,0.45-3.56-1.34-3.56-1.34s-3.2,2.23-3.89,1.62--}}
{{--	c-0.6-0.53,0.65-4.13,0.65-4.13s-3-2.19-2.84-2.8C51.85,11.68,55.55,11.89,55.55,11.89z"--}}
{{--                                              data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(255, 255, 255); --darkreader-inline-fill: #e8e6e3;" d="M97.01,95.33c1.21,0.67,2.73,0.29,3.29-1c0.51-1.15-0.43-2.52-1.28-2.89--}}
{{--	c-0.85-0.37-2.34,0.12-2.88,1.09C95.61,93.49,96.28,94.93,97.01,95.33z" data-darkreader-inline-fill=""></path>--}}
{{--                                        <path style="fill: rgb(255, 255, 255); --darkreader-inline-fill: #e8e6e3;" d="M114.19,65.84c-0.69-1.07-2.18-1.42-3.15-0.56c-0.94,0.84-0.71,2.16-0.18,2.83--}}
{{--	c0.53,0.67,1.95,0.92,2.81,0.37S114.61,66.48,114.19,65.84z" data-darkreader-inline-fill=""></path>--}}
{{--</svg>--}}

{{--                                    Get Started--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="divider divider-vertical md:divider-horizontal my-8 md:my-0">OR</div>--}}
{{--                        <div class="flex w-full flex-col space-y-3 md:space-y-0 justify-between items-center">--}}
{{--                            <div class="mb-8">--}}
{{--                                <div class="mx-auto">--}}
{{--                                    <p class="text-3xl font-bold tracking-tight text-center text-gray-700 sm:text-4xl">--}}
{{--                                        Contact us to<br>--}}
{{--                                        <span class="block md:inline text-primary-500 tracking-wider">learn more</span>--}}
{{--                                    </p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="my-auto flex">--}}
{{--                                <a class="btn btn-base btn-outline btn-lg w-72" data-role="app-link"--}}
{{--                                   data-event="Click Contact Us"--}}
{{--                                   data-event-props="{&quot;location&quot;:&quot;Bottom of the landing page&quot;}"--}}
{{--                                   href="/contact" data-event-initialized="true">--}}
{{--                                    <svg version="1.1" class="h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg"--}}
{{--                                         xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"--}}
{{--                                         viewBox="0 0 128 128" style="enable-background:new 0 0 128 128;"--}}
{{--                                         xml:space="preserve">--}}
{{--<g>--}}
{{--    <g>--}}
{{--        <path style="fill: rgb(255, 202, 40); --darkreader-inline-fill: #ffcd35;" d="M17.57,62.68c-2.79-4.01-3.91-7.79-1.18-10.08c2.31-1.94,5.71-2.31,9.91,2.54--}}
{{--			c0,0,12.55,14.58,16.87,18.63c0.98,0.91,2.2,1.05,3.31-0.1c0.94-0.98,0.6-1.83-0.2-3c0,0-17.69-26.3-20.01-29.51--}}
{{--			c-3.87-5.37-2.38-8.84-0.59-10.49c2.49-2.31,6.87-2.77,10.94,2.81l21.42,28.67c0.65,0.69,1.7,0.79,2.47,0.25--}}
{{--			c0.1-0.07,0.2-0.14,0.3-0.21c0.79-0.56,1.02-1.63,0.54-2.47c-3.75-6.53-18.67-32.55-20.86-37.29c-2.52-5.47-1.44-8.25,1.23-9.86--}}
{{--			c3.17-1.91,6.15-1.77,9.71,3.52c3.86,5.76,18.85,30.01,22.66,36.53c0.5,0.85,1.57,1.17,2.45,0.74c0.01-0.01,0.03-0.01,0.04-0.02--}}
{{--			c0.84-0.41,1.6-1.24,1.25-2.42c-2.08-6.86-12.29-28.22-14.43-33.13c-2.93-6.71-1.5-8.99,1.53-10.53c3.18-1.61,6.49-0.34,8.74,4.14--}}
{{--			c1.52,3.04,28.21,51.61,28.21,51.61c-0.39-7.24,1.44-12.37,3-17.52c2.84-9.37,9.71-14.24,14.69-12.05--}}
{{--			c2.59,1.14,3.06,3.41,2.78,5.15c-0.56,3.38-2.94,13.85-3.4,22.05c-1.08,19.22,4.73,41.37-16.92,55.29--}}
{{--			c-14.49,9.32-30.02,7.68-40.28,0.51C49.31,107.75,19.85,65.96,17.57,62.68z"--}}
{{--              data-darkreader-inline-fill=""></path>--}}
{{--        <path style="fill: rgb(237, 166, 0); --darkreader-inline-fill: #ffbe26;" d="M117.68,51.77c-1.81,7.61-2.05,16.95-1.99,20.93c0.24,15.51,0.16,28.93-15.39,41.19--}}
{{--			c-1.91,1.51-7.9,5.19-14.87,7.11c-2.24,0.61-1.39,1.33-0.01,1.17c7.61-0.88,13.38-4.16,16.59-6.23--}}
{{--			c21.64-13.92,16.32-35.92,17.4-55.14c0.46-8.2,2.96-21.1,2.92-22.21C122.28,37.48,119.49,44.16,117.68,51.77z"--}}
{{--              data-darkreader-inline-fill=""></path>--}}
{{--        <path style="fill: rgb(237, 166, 0); --darkreader-inline-fill: #ffbe26;" d="M77.53,52.57c0,0-1.55,0.01-3.02-2.04C70.1,44.4,56.27,21.25,52.29,15.57--}}
{{--			c-4.54-6.48-8.77-4-9.73-3.48c0,0,3.48,0.12,5.4,2.97c3.92,5.81,18.78,31.85,23.47,37.44C74.46,56.11,77.53,52.57,77.53,52.57z"--}}
{{--              data-darkreader-inline-fill=""></path>--}}
{{--        <path style="fill: rgb(237, 166, 0); --darkreader-inline-fill: #ffbe26;" d="M22.71,54.54c1.36,1.46,13.3,15.63,17.7,19.58c3.78,3.39,6.6-0.93,6.6-0.93s-1.39,0.19-3.29-1.24--}}
{{--			c-4.72-3.57-15.9-16.3-18.42-18.98c-3.74-3.98-7.22-1.49-7.96-1.04C17.35,51.93,19.49,51.1,22.71,54.54z"--}}
{{--              data-darkreader-inline-fill=""></path>--}}
{{--        <path style="fill: rgb(237, 166, 0); --darkreader-inline-fill: #ffbe26;" d="M61.59,61.26c0,0-1.28,0.93-3.63-1.82c-1.71-2.01-21.34-25.96-21.34-25.96--}}
{{--			c-4.92-6.01-8.79-4.22-9.59-3.76c0,0,2.47-0.13,5.36,3.2c1.32,1.52,22.27,28.23,23.11,29.14C58.57,65.41,61.41,62.53,61.59,61.26z--}}
{{--			" data-darkreader-inline-fill=""></path>--}}
{{--        <path style="fill: rgb(237, 166, 0); --darkreader-inline-fill: #ffbe26;" d="M101.8,57.83c0,0-26.01-43.61-27.67-46.58c-3.69-6.59-7.98-4.57-8.71-4.24c0,0,2.39-0.68,5.2,4.1--}}
{{--			c1.5,2.54,20.61,36.9,26.71,47.88c0.48,2.05,0.44,4.72-1.1,6.44c-5.52,6.19-12.56,12.51-10.49,28.18--}}
{{--			c0.52,3.97,1.99,7.73,3.08,9.54c1.38,2.27,2.97,1.41,2.27-0.29c-0.47-1.15-1.22-3.86-1.37-5.02c-0.7-5.4-3.06-14.84,7.52-26.16--}}
{{--			C99.03,69.79,103.58,64.08,101.8,57.83z" data-darkreader-inline-fill=""></path>--}}
{{--    </g>--}}
{{--    <g>--}}
{{--        <path style="fill: rgb(176, 190, 197); --darkreader-inline-fill: #bdb7ae;" d="M103.49,30.96c-1.39-4.93-3.55-9.45-6.35-13.37c-2.52-3.53-5.57-6.57-9.05-9.01--}}
{{--			c-0.44-0.31-1-1.27-0.52-2.2s1.52-0.81,1.91-0.65c4.35,1.79,7.91,4.88,10.85,9.12c3.49,5.04,5.22,9.79,6.11,15.1--}}
{{--			c0.1,0.58,0.08,1.8-1.16,2.12C104.05,32.39,103.63,31.44,103.49,30.96z" data-darkreader-inline-fill=""></path>--}}
{{--    </g>--}}
{{--    <g>--}}
{{--        <path style="fill: rgb(176, 190, 197); --darkreader-inline-fill: #bdb7ae;" d="M96.69,36.25c-1.39-4.93-3.55-9.45-6.35-13.37c-2.52-3.53-5.57-6.57-9.05-9.01--}}
{{--			c-0.44-0.31-1-1.27-0.52-2.2c0.48-0.94,1.52-0.81,1.91-0.65c4.35,1.79,7.91,4.88,10.85,9.12c3.49,5.04,5.22,9.79,6.11,15.1--}}
{{--			c0.1,0.58,0.08,1.8-1.16,2.12C97.26,37.68,96.83,36.74,96.69,36.25z" data-darkreader-inline-fill=""></path>--}}
{{--    </g>--}}
{{--    <g>--}}
{{--        <path style="fill: rgb(176, 190, 197); --darkreader-inline-fill: #bdb7ae;" d="M8.56,77.46c1.72,4.83,4.17,9.19,7.22,12.92c2.75,3.35,5.99,6.19,9.62,8.4--}}
{{--			c0.46,0.28,1.08,1.2,0.66,2.16c-0.42,0.97-1.46,0.91-1.87,0.78c-4.46-1.5-8.22-4.35-11.43-8.39c-3.81-4.8-5.86-9.42-7.1-14.66--}}
{{--			c-0.14-0.58-0.2-1.79,1.02-2.19C7.9,76.08,8.39,76.99,8.56,77.46z" data-darkreader-inline-fill=""></path>--}}
{{--    </g>--}}
{{--    <g>--}}
{{--        <path style="fill: rgb(176, 190, 197); --darkreader-inline-fill: #bdb7ae;" d="M14.99,71.73c1.72,4.83,4.17,9.19,7.22,12.92c2.75,3.35,5.99,6.19,9.62,8.4--}}
{{--			c0.46,0.28,1.08,1.2,0.66,2.16c-0.42,0.97-1.46,0.91-1.87,0.78c-4.46-1.5-8.22-4.35-11.43-8.39c-3.81-4.8-5.86-9.42-7.1-14.66--}}
{{--			c-0.14-0.58-0.2-1.79,1.02-2.19C14.33,70.34,14.82,71.26,14.99,71.73z" data-darkreader-inline-fill=""></path>--}}
{{--    </g>--}}
{{--</g>--}}
{{--</svg>--}}

{{--                                    Contact Us--}}
{{--                                </a></div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </section>--}}
{{--    </div>--}}
</div>
