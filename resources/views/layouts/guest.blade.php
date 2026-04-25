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
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-emerald-600 relative overflow-hidden">

        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none opacity-20">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full bg-emerald-900 blur-3xl"></div>
        </div>

        <div class="z-10 text-center flex flex-col items-center">
            <a href="/">
                <img src="{{ asset('logo-pegadaian.png') }}" alt="Logo Pegadaian" class="h-24 w-auto drop-shadow-md hover:scale-105 transition-transform duration-300">
            </a>

            <div class="mt-5 bg-white/20 backdrop-blur-sm border border-white/30 text-white font-extrabold tracking-widest px-5 py-1.5 rounded-full shadow-lg text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                ADMIN ONLY
            </div>
        </div>

        <div class="z-10 w-full sm:max-w-md mt-8 px-8 py-8 bg-white shadow-2xl overflow-hidden sm:rounded-3xl border border-slate-100">
            {{ $slot }}
        </div>

        <p class="mt-8 text-emerald-100 text-xs z-10 font-medium">
            &copy; {{ date('Y') }} cabang Kebayoran Lama
        </p>
    </div>
</body>
</html>
