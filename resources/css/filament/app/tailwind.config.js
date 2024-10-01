import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    darkMode: 'class',
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    plugins: [
        require('tailwindcss-animated'),
    ]
}
