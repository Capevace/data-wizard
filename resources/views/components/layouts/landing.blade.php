<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <x-seo::meta />

        <?php
            \Filament\Support\Facades\FilamentColor::register([
                'primary' => \Filament\Support\Colors\Color::Blue,
                'warning' => \Filament\Support\Colors\Color::Yellow,
                'success' => \Filament\Support\Colors\Color::Emerald,
                'gray' => \Filament\Support\Colors\Color::Slate,
            ]);

//              Averia Serif Libre
            filament()->getCurrentPanel()->font('Averia Serif Libre');

        ?>

        @filamentStyles

        @if ($favicon = filament()->getFavicon())
            <link rel="icon" href="{{ $favicon }}" />
        @endif

        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontHtml() }}

        <style>
            :root {
                --font-family: {{ filament()->getFontFamily() }};
            }
        </style>

        @vite([
            'resources/css/filament/app/theme.css',
            'resources/css/landing.css',
            'resources/js/src/landing.ts'
        ])

        <x-plausible />
    </head>
    <body class="antialiased font-sans">
        {{ $slot }}
    </body>
</html>
