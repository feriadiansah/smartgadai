<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- TAMBAHKAN BARIS INI UNTUK MEMANGGIL IKON FONT AWESOME -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#f4f7fe]">
    <div class="flex min-h-screen">
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col min-w-0">
            <header class="flex items-center justify-between px-8 py-4 bg-transparent">
                <div>
                    @isset($header)
                        <div class="text-[#1b2559] text-3xl font-bold">
                            {{ $header }}
                        </div>
                    @endisset
                </div>

                <div class="flex items-center gap-3 bg-white p-2 px-4 rounded-full shadow-sm">
                    <p class="text-sm font-bold text-[#1b2559]">{{ Auth::user()->name }}</p>
                    <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </header>

            <main class="p-8 pt-2">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
{{-- <!DOCTYPE html>
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
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html> --}}
