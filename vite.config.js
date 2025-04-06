import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/filament/app/theme.css',
                'resources/css/landing.css',
                'resources/js/src/magic-extract.ts',
                'resources/js/src/landing.ts',
            ],
            refresh: true,
        }),
    ],
});
