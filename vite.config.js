import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/filament/app/theme.css',
                'resources/js/src/magic-extract.ts',
                'packages/shadowy-theme/resources/css/theme.css',
            ],
            refresh: true,
        }),
    ],
});
