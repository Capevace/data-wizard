<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MagicImport</title>

    <script src="//cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=averia-serif-libre:300,300i,400,400i,700,700i|abel:200,400,600,700" rel="stylesheet" />

    <style>
        body {
            font-family: 'Averia Serif Libre', sans-serif;
            font-weight: 300 !important;
        }

        body p {
            opacity: 0.9;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Averia Serif Libre', sans-serif;
            font-weight: 500 !important;
        }
    </style>

    <script>
        window.MagicImport = {};
    </script>

    @stack('components')
</head>
<body class="bg-sky-950 text-sky-50 dark flex flex-col min-h-screen">
    {{ $slot }}
    @yield('content')

    <script src="//unpkg.com/alpinejs"></script>
{{--    @stack('scripts')--}}
</body>
</html>
