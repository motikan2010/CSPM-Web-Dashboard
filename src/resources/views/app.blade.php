<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @production
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp
        <script type="module" src="/build/{{ $manifest['resources/js/app.js']['file'] }}"></script>
        <link rel="stylesheet" href="/build/{{ $manifest['resources/js/app.js']['css'][0] }}"/>
    @else
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @endproduction
</head>
<body class="antialiased">
<div id="app"></div>
</body>
</html>
