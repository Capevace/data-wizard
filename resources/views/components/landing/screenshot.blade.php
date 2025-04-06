<div class="group">
    <div class="relative rounded-xl overflow-hidden shadow-2xl border border-white/10 mb-4 transition-transform duration-300 ease-in-out group-hover:scale-[1.03]">
        {{-- Placeholder background gradient --}}
        <div class="bg-gradient-to-br from-gray-700 via-gray-800 to-gray-900 flex items-center justify-center">
{{--                         <x-icon name="lucide-image" class="h-16 w-16 text-gray-500"/>--}}
             <img x-zoomable
                 src="{{ $src }}" {{-- Replace with actual path --}}
                 alt="{{ $alt }}" class="w-full h-full object-contain"/>
        </div>
    </div>
    <h3 class="text-lg font-semibold text-center text-white mb-1">{{ $title }}</h3>
    <p class="text-center text-gray-300 text-sm max-w-md mx-auto">
        {{ $slot }}
    </p>
</div>
