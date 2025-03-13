<!doctype html>
<html lang="de" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @php
        $title = strip_tags(($livewire ?? null)?->getTitle() ?? '');
        $brandName = strip_tags(filament()->getBrandName());
    @endphp

    <title>{{ filled($title) ? "{$title} - " : null }} {!! $brandName !!}</title>


    @php
//        \Filament\Support\Facades\FilamentColor::register([
//            'primary' => \Filament\Support\Colors\Color::Neutral,
//            'warning' => \Filament\Support\Colors\Color::Yellow,
//            'success' => \Filament\Support\Colors\Color::Emerald,
//            'gray' => \Filament\Support\Colors\Color::Neutral,
//        ]);


        \Filament\Support\Facades\FilamentColor::register([
            'primary' => \Filament\Support\Colors\Color::Teal,
            'warning' => \Filament\Support\Colors\Color::Yellow,
            'success' => \Filament\Support\Colors\Color::Teal,
            'danger' => \Filament\Support\Colors\Color::Rose,
            'gray' => \Filament\Support\Colors\Color::Slate,
        ]);
    @endphp

    {{ filament()->getPanel('app')->getTheme()->getHtml() }}
    {{ filament()->getPanel('app')->getFontHtml() }}

    <style>
        :root {
            --font-family: '{!! filament()->getFontFamily() !!}';
            --sidebar-width: {{ filament()->getSidebarWidth() }};
            --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
            --default-theme-mode: {{ filament()->getDefaultThemeMode()->value }};
        }
    </style>

{{--    <script src="//cdn.tailwindcss.com"></script>--}}
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {

                }
            }
        }
    </script>

    <script>
        const theme = localStorage.getItem('theme') ?? @js(filament()->getDefaultThemeMode()->value)

        if (
            theme === 'dark' ||
            (theme === 'system' &&
                window.matchMedia('(prefers-color-scheme: dark)')
                    .matches)
        ) {
            document.documentElement.classList.add('dark')
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @vite(['resources/css/filament/app/theme.css'])

    {!! \Filament\Support\Facades\FilamentAsset::renderStyles() !!}

    @livewireStyles
    @stack('styles')

    <style>
        /*body {*/
        /*    font-family: 'Averia Serif Libre', sans-serif;*/
        /*    font-weight: 300 !important;*/
        /*}*/

        /*body p {*/
        /*    opacity: 0.9;*/
        /*}*/

        /*h1, h2, h3, h4, h5, h6 {*/
        /*    font-family: 'Averia Serif Libre', sans-serif;*/
        /*    font-weight: 500 !important;*/
        /*}*/
    </style>

    @if ($favicon = filament()->getFavicon())
        <link rel="icon" href="{{ $favicon }}" />
    @endif

    <script>
        window.MagicImport = {};
    </script>

    @if (config('app.external_css_url'))
        <link rel="stylesheet" href="{{ config('app.external_css_url') }}">
    @endif

    @stack('components')
</head>
<body class="flex flex-col min-h-screen dark:bg-gray-950 antialiased">
    {{ $slot }}
    @yield('content')

    @livewire('notifications')

    @stack('scripts')
    @filamentScripts(withCore: true)

    @livewireScripts

{{--    <x-pan-analytics::viewer />--}}
</body>
</html>
