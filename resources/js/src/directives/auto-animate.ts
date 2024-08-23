import type { Alpine } from 'alpinejs';
import autoAnimate from '@formkit/auto-animate';

export default function AutoAnimatePlugin(Alpine: Alpine) {
	Alpine.directive('auto-animate', (el) => {
		autoAnimate(el, {
			disrespectUserMotionPreference: true,
		});
	});
}
