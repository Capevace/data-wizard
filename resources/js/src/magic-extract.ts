import type { Alpine } from 'alpinejs';
import AutoAnimatePlugin from './directives/auto-animate';

declare global {
	interface Window {
		Alpine: Alpine;
	}
}

window.addEventListener('alpine:init', (event) => {
    console.info('App initialized');

	window.Alpine.plugin(AutoAnimatePlugin);
});
