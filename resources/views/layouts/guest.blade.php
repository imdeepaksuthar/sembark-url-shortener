<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sembark URL Shortener') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="flex flex-col items-center">
            <a href="/" class="flex flex-col items-center gap-2.5 group">
                <x-application-logo class="w-16 h-16 drop-shadow-md transition duration-200 transform group-hover:scale-105" />
                <div class="text-center">
                    <span class="text-2xl font-bold tracking-tight text-gray-900 block">
                        Sembark <span class="text-indigo-600">URL Shortener</span>
                    </span>
                    <span class="text-xs text-gray-500 font-medium block mt-0.5">
                        Smart, Fast & Secure Link Shortening
                    </span>
                </div>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>