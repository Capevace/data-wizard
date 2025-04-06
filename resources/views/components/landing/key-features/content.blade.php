<div
	{{ $attributes->class('p-8 text-xl overflow-hidden feature-content') }}
	x-transition:enter="transition ease-out duration-500 transform"
	x-transition:enter-start="opacity-0 translate-y-5"
	x-transition:enter-end="opacity-100 translate-y-0"
	x-transition:leave="transition ease-in duration-300 transform absolute inset-0"
	x-transition:leave-start="opacity-100 translate-y-0"
	x-transition:leave-end="opacity-0 translate-y-3"
>
	{{ $slot }}
</div>
