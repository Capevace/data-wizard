<div {{ $attributes->class('mx-auto w-full max-w-screen-lg px-5 pt-20 min-[550px]:px-10') }}>
    <div class="relative flex items-start justify-center min-[550px]:justify-start lg:gap-40 xl:justify-between ">
        <div class="min-[500px]:pl-10 sm:shrink-0 sm:pl-14 xl:pl-0">
            {{--            <div--}}
            {{--                class="relative translate-x-10 text-3xl font-black italics min-[500px]:translate-x-0 lg:text-4xl ml-2 mb-1"--}}
            {{--            >--}}
            {{--                <div x-ref="accelerated"--}}
            {{--                     class="bg-gradient-to-r from-primary-500 to-primary-100 bg-clip-text text-transparent"--}}
            {{--                     style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;">--}}
            {{--                    Quickly add powerful--}}
            {{--                </div>--}}


            {{--                <div x-ref="shadow" class="absolute left-1.5 top-1 -z-10 select-none text-primary-500/30"--}}
            {{--                     style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;">--}}
            {{--                    Quickly add powerful--}}
            {{--                </div>--}}
            {{--            </div>--}}

            <div
                class="animate-fade-up animate-duration-[1.4s] animate-delay-100 relative translate-x-10 text-3xl font-black italics min-[500px]:translate-x-0 ml-10 mb-4"
            >
                <div x-ref="accelerated"
                     class="bg-gradient-to-r from-gray-500 to-gray-400 bg-clip-text text-transparent"
                     style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;">
                    Embed
                    <span class="inline-block relative bg-gradient-to-r from-gray-500 to-gray-400 bg-clip-text text-transparent">
                        <div class="star-container absolute -ml-4 -mt-2 top-0 right-0 flex items-center gap-2">
                            <style>
                                @keyframes star-animation {
                                    0% {
                                        opacity: 0;
                                        transform: translate(0px, 0px) rotate(-16deg);
                                        color: #d1d183;
                                    }
                                    20% {
                                        opacity: 0.2;
                                        color: #9a9a5a;
                                    }
                                    80% {
                                        opacity: 1;
                                        color: #f6f6d9;
                                    }
                                    100% {
                                        opacity: 0;
                                        transform: translate(0px, -15px) rotate(60deg);
                                        color: #d1d183;
                                    }
                                }

                                @keyframes star-animation-intro {
                                    0% {
                                        opacity: 0;
                                    }
                                    50% {
                                        opacity: 0;
                                    }
                                    100% {
                                        opacity: 1;
                                    }
                                }

                                .star-container {
                                    animation: star-animation-intro 3s ease-out;
                                }

                                .star {
                                    --speed: 2s;
                                    width: .4rem;
                                    color: #d1d183;
                                    animation: star-animation 1s infinite cubic-bezier(0.61, 1, 0.88, 1);
                                }

                                .star:nth-child(1) {
                                    animation-delay: 0s;
                                    animation-duration: calc(var(--speed) + 1s);
                                    margin-top: 0.8rem;
                                }
                                .star:nth-child(2) {
                                    animation-delay: 0.1s;
                                    animation-duration: calc(var(--speed) + 1.7s);
                                    margin-top: 0.5rem;
                                }
                                .star:nth-child(3) {
                                    animation-delay: 1.5s;
                                    animation-duration: calc(var(--speed) + 1.5s);
                                    margin-top: 1.4rem;
                                }
                                .star:nth-child(4) {
                                    animation-delay: 0.3s;
                                    animation-duration: calc(var(--speed) + 1.2s);
                                    margin-top: 1rem;
                                }
                                .star:nth-child(5) {
                                    animation-delay: 0.4s;
                                    animation-duration: calc(var(--speed) + 1.1s);
                                    margin-top: 0.8rem;
                                }
                                .star:nth-child(6) {
                                    animation-delay: 1.5s;
                                    animation-duration: calc(var(--speed) + 1.8s);
                                    margin-top: 0.3rem;
                                }

                                .star:nth-child(7) {
                                    animation-delay: 2.5s;
                                    animation-duration: calc(var(--speed) + 2.5s);
                                    margin-top: 0.2rem;
                                }

                                .star:nth-child(8) {
                                    animation-delay: 3.5s;
                                    animation-duration: calc(var(--speed) + 3.5s);
                                    margin-top: 0.1rem;
                                }
                            </style>
                            <x-icon
                                name="heroicon-s-star"
                                class="w-4 h-4 star"
                            />
                            <x-icon name="heroicon-s-star" class="star drop-shadow-sm" />
                            <x-icon name="heroicon-s-star" class="star drop-shadow-sm" />
                            <x-icon name="heroicon-s-star" class="star drop-shadow-sm" />
                            <x-icon name="heroicon-s-star" class="star drop-shadow-sm" />
                            <x-icon name="heroicon-s-star" class="star drop-shadow-sm" />
                            <x-icon name="heroicon-s-star" class="star drop-shadow-sm" />
                        </div>

                        <span class="z-10">magical</span>
                    </span>
                </div>
            </div>


            <div
                class="relative translate-x-10 text-7xl font-black italics min-[500px]:translate-x-0  text-left mb-4 mr-20"
            >
                <div
                     class="animate-fade-left animate-duration-[1.4s]  bg-gradient-to-br from-primary-500 via-primary-400 to-primary-500 bg-clip-text text-transparent"
                     style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;">
                    <div class="">JSON extraction</div>
                    <div class="text-4xl mt-2 ml-12">from PDFs, docs and images</div>
                </div>


                <div class="animate-fade-left animate-duration-[1.4s]   absolute -right-0.5 top-0.5 -z-10 select-none text-primary-500/30"
                     style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;">
                    <div>JSON extraction</div>
                    <div class="text-4xl  mt-2 ml-12">from PDFs, docs and images</div>
                </div>
            </div>


            <div class="group/header">
                {{--                <div class="relative space-y-3 font-black">--}}
                {{--                    <div x-ref="title"--}}
                {{--                         style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;">--}}
                {{--                        <div class="relative max-w-fit text-6xl lg:text-7xl">--}}
                {{--                            JSON extraction--}}
                {{--                        </div>--}}
                {{--                        <div class="text-4xl lg:text-5xl">--}}
                {{--                            from PDFs, docs and images--}}
                {{--                            </span>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                {{--                    <div--}}
                {{--                        class="absolute -left-14 top-1 lg:-left-20"--}}
                {{--                        style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;"--}}
                {{--                    >--}}
                {{--                        @svg('heroicon-o-document-magnifying-glass', 'opacity-50 w-10 h-10 transform -rotate-[34deg] ')--}}
                {{--                    </div>--}}
                {{--                </div>--}}
                <div
                    class="animate-fade-down animate-delay-300  relative translate-x-10 text-3xl font-black italics min-[500px]:translate-x-0 text-right  mb-10 mr-20"
                >
                    <div x-ref="accelerated"
                         class="bg-gradient-to-r from-gray-300 to-gray-500 bg-clip-text text-transparent"
                         style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;">
                        directly in your forms!
                    </div>
                </div>

                <div class="relative pt-5">
                    <ul
                        class="text-lg font-medium leading-normal opacity-90 lg:text-xl list-inside pl-5 text-gray-500 flex flex-col gap-2 [&>li>svg]:animate-pulse"
                        style="--speed: 3;"
                    >
                        <li class="flex items-center gap-3">
                            <x-icon name="bi-braces" class="w-4 h-4 text-success-600"
                                    style="animation-delay: calc(var(--speed, 1) * 0.2s)"/>
                            <span class="font-medium text-gray-800">Only needs a JSON schema</span>
                        </li>
                        <li class="flex items-center gap-3">
                            {{--                            <x-icon name="bi-terminal" class="w-4 h-4 text-success-600"/>--}}
                            <svg class="text-success-600 w-4 h-4" width="32" height="32" viewBox="0 0 32 32"
                                 xmlns="http://www.w3.org/2000/svg"
                                 style="animation-delay: calc(var(--speed, 1) * 0.3s)">
                                <path fill="currentColor" fill-rule="evenodd" d="m25 21l7 5l-7 5z"/>
                                <path fill="currentColor"
                                      d="m20.17 19l-2.59 2.59L19 23l4-4l-4-4l-1.42 1.41zm-8.34 0l2.59-2.59L13 15l-4 4l4 4l1.42-1.41z"/>
                                <circle cx="9" cy="8" r="1" fill="currentColor"/>
                                <circle cx="6" cy="8" r="1" fill="currentColor"/>
                                <path fill="currentColor"
                                      d="M21 26H4V12h24v7h2V6c0-1.102-.897-2-2-2H4c-1.103 0-2 .898-2 2v20c0 1.103.897 2 2 2h17zM4 6h24v4H4z"/>
                            </svg>
                            <span>Optimized for <span
                                    class="font-medium text-gray-800">embedding via iFrame & Webhook</span></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-icon name="bi-braces" class="w-4 h-4 text-success-600"
                                    style="animation-delay: calc(var(--speed, 1) * 0.2s)"/>
                            <span><span class="font-medium text-gray-800">Automatic UIs</span> from JSON Schema</span>
                        </li>

                        <li class="flex items-center gap-3">
                            <x-icon name="bi-robot" class="w-4 h-4 text-success-600"
                                    style="animation-delay: calc(var(--speed, 1) * 0.5s)"/>
                            <span class="font-medium text-gray-800">Multiple LLMs and providers supported</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-icon name="bi-body-text" class="w-4 h-4 text-success-600"
                                    style="animation-delay: calc(var(--speed, 1) * 0.6s)"/>
                            <span>Reviews & fixes data before sending to your application</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <x-icon name="bi-code-slash" class="w-4 h-4 text-success-600"
                                    style="animation-delay: calc(var(--speed, 1) * 0.1s)"/>
                            <span>Open-source and self-hostable</span>
                        </li>
                    </ul>
                    <div class="absolute -right-10 top-1 min-[500px]:right-10 lg:-right-5">
                        @svg('heroicon-o-document-magnifying-glass', 'opacity-50 w-10 h-10 transform -rotate-[-28deg] ')
                    </div>
                </div>
            </div>


            <div class="flex flex-col gap-5 pt-10 text-white min-[500px]:flex-row min-[500px]:items-center">

                <x-filament::button
                    size="xl"
                    color="success"
                    href="{{ config('app.github_url') }}"
                    tag="a"
                    target="_blank"
                    icon="bi-github"
                    icon-position="after"
                    class="bg-gradient-to-br from-purple-300/50 hover:from-orange-300 to-emerald-600 transition-colors"
                >
                    <span class="text-lg ml-2">
                        100% Open Source
                    </span>
                </x-filament::button>

                <x-filament::button
                    size="xl"
                    color="primary"
                    href="#"
                    tag="a"
                    target="_blank"
                    icon="heroicon-o-arrow-right"
                    icon-position="after"
                    class="bg-gradient-to-br from-teal-300/50 hover:from-indigo-300 to-indigo-600 transition-colors"
                >
                    <span class="text-lg ml-2">
                        Run with Docker
                    </span>
                </x-filament::button>
            </div>


            <div class="hidden -translate-x-16 pt-2 min-[500px]:block lg:-translate-x-32">
                <img
                    src="https://filamentphp.com/build/assets/decoration-up-arrow-red-5661465e.svg"
                    alt="Up arrow" class="w-32"
                    style="translate: none; rotate: none; scale: none; transform: translate(0px, 0px); opacity: 1; visibility: inherit;">
            </div>
        </div>


        <div class="absolute -top-10 -right-32 md:-right-32 -z-10 opacity-20 lg:opacity-80">
            <div class="relative">
                <div class="w-[25rem] lg:w-[30rem]">
                    <img src="{{ url('images/wizard1.png') }}"
                         class="w-full"
                         style="translate: none; rotate: none; scale: none; transform: translate3d(16.2985px, -16.2985px, 0px); opacity: 1; visibility: inherit;">
                </div>
            </div>
        </div>
    </div>
</div>
