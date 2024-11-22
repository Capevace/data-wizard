import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Clusters/Products/**/*.php',
        './resources/views/filament/clusters/products/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
    ],
}
