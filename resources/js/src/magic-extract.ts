import type { Alpine } from 'alpinejs';
import AutoAnimatePlugin from './directives/auto-animate';
import JsonEditorPlugin from './components/json-editor';
import {GeneratorComponent} from "./components/generator";

declare global {
	interface Window {
		Alpine: Alpine;
	}
}

window.addEventListener('alpine:init', (event) => {
    console.info('App initialized');

    window.Alpine.data('GeneratorComponent', GeneratorComponent);
	window.Alpine.plugin(AutoAnimatePlugin);
    window.Alpine.plugin(JsonEditorPlugin);
});
