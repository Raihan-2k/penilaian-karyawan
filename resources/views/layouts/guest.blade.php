<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">

    {{-- Background Image Fullscreen --}}
    <div class="min-h-screen bg-cover bg-center bg-fixed flex items-center justify-center">

        {{-- Kontainer Konten --}}
        <div class="w-full sm:max-w-md px-6 py-6 bg-white bg-opacity-90 shadow-md rounded-xl backdrop-blur-sm">
            {{ $slot }}
        </div>

    </div>
</body>

</html>
