@props([
	'activeFeature' => null,
])

<div
	:class="{
		'bg-white/10 border-l-cyan-400 text-white scale-[1.03] shadow-lg': activeFeature === {{ $activeFeature }},
		'bg-white/[0.02] border-l-transparent hover:bg-white/5 text-gray-400 hover:text-gray-200': activeFeature !== {{ $activeFeature }},
	}"
	{{ $attributes->class('cursor-pointer w-full text-left px-5 py-4 rounded-lg border-l-2 transition-all duration-300 ease-in-out transform group focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400 focus-visible:ring-offset-2 focus-visible:ring-offset-[#0A0F1A]') }}
>
	{{ $slot }}
</div>
