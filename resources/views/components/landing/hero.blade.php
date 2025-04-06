<div class="relative overflow-hidden pb-16 pt-6">
    {{-- Animated Background Glows --}}
    <div class="absolute -top-40 -left-40 w-[40rem] h-[40rem] bg-gradient-radial from-cyan-500/20 via-purple-500/10 to-transparent blur-3xl animate-pulse"></div>
    <div class="absolute -bottom-60 -right-20 w-[30rem] h-[30rem] bg-gradient-radial from-fuchsia-500/15 via-blue-500/5 to-transparent blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>

    {{-- Wizard Image Placeholder --}}
    <img src="{{ url('images/wizard1.png') }}" {{-- Replace with actual path --}}
         alt="Data Wizard Mascot"
         class="absolute top-0 right-0 w-[55rem] h-auto {{-- Adjust size as needed --}}
                opacity-20
                mr-[90px]
                mt-[200px]
                -z-0 {{-- Behind text and nav but above absolute bg --}}
                pointer-events-none {{-- Ignore mouse events --}}
                transform translate-x-1/4 -translate-y-1/4 {{-- Adjust positioning --}}
                blur {{-- Optional subtle blur --}}
                hidden lg:block" {{-- Hide on smaller screens --}}
         aria-hidden="true"
    />

    {{-- Navigation --}}
    <nav class="relative z-10 container mx-auto px-6 py-6">
         <div class="flex items-center flex-col gap-10 md:gap-0 md:flex-row justify-between">
            <a href="/" class="relative flex items-center space-x-2 group">
                <img
                    src="{{ asset('images/logo.svg') }}"
                    class="h-20"
                    alt="Data Wizard Logo"
                />
                <span class="text-sm absolute left-0 bottom-0 transform translate-x-[4.2rem] translate-y-2 opacity-50">A project by Lukas Mateffy</span>
            </a>
            <div class="flex items-center space-x-6">
                <a href="{{ config('landing.github-url') }}" target="_blank" rel="noopener noreferrer" class="flex items-center space-x-2 text-gray-300 hover:text-cyan-300 transition-colors">
                    <x-icon name="lucide-github" class="h-5 w-5"/>
                    <span>GitHub</span>
                </a>
                <a href="{{ config('landing.documentation-url') }}" class="flex items-center space-x-2 text-gray-300 hover:text-cyan-300 transition-colors">
                    <x-icon name="lucide-file-text" class="h-5 w-5"/>
                    <span>Docs</span>
                </a>
                <a href="https://mateffy.me" class="flex-shrink-0 flex items-center space-x-2 text-gray-300 hover:text-cyan-300 transition-colors">
                    <x-icon name="fas-user-ninja" class="h-5 w-5"/>
                    <span>Made by Lukas Mateffy</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- Hero Content --}}
    <div class="relative z-10 container mx-auto px-6 pt-16 pb-24 text-left">
        <div class="max-w-4xl">
             {{-- Apply animated gradient class --}}
            <h1
                class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-white via-cyan-300 to-fuchsia-400 text-shadow-md animated-hero-gradient min-h-[8rem]"
            >
                Extract Structured Data from Any Document with LLMs
            </h1>
            <p class="text-xl md:text-2xl text-gray-300 mb-14 text-shadow">
                Turn documents like PDFs, Word files, and images into structured, validated JSON using AI. Open-source and easy to integrate into your applications.
            </p>
            <div class="grid md:grid-cols-2 md:w-fit items-center text-lg gap-4">
                <a href="{{ config('landing.quick-start-url') }}" class="flex items-center gap-5 justify-between px-8 py-3 bg-white/90 text-gray-950 rounded-lg font-semibold backdrop-blur-sm hover:bg-white/80 transition-colors border border-white/20 transform hover:scale-105">
                    Get started in 2 minutes
                    <x-icon name="lucide-arrow-right" class="h-5 w-5 inline-block"/>
                </a>
                <a href="{{ config('landing.documentation-url') }}" class="flex items-center gap-5 justify-between px-8 py-3 bg-white/10 rounded-lg font-semibold backdrop-blur-sm hover:bg-white/20 transition-colors border border-white/20 transform hover:scale-105">
                    View Documentation
                    <x-icon name="lucide-book" class="h-5 w-5 inline-block"/>
                </a>
            </div>
            <div class="grid md:grid-cols-2 md:w-fit items-center text-lg gap-4 mt-6">
                <a
                    href="{{ route('filament.app.auth.login') }}"
                    class="flex items-center gap-2 text-gray-300 hover:text-cyan-300 transition-colors opacity-50 hover:opacity-100 transition-opacity"
                >
                    <x-icon name="bi-mortarboard" class="h-5 w-5"/>
                    <span>Academic Login for Students and Professors</span>
                    <x-icon name="bi-arrow-right" class="h-5 w-5"/>
                </a>
            </div>
        </div>
    </div>
</div>
