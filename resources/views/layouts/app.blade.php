<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Pegadaian') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800">
    <div class="min-h-screen bg-slate-50 relative">

        @include('layouts.navigation')

        <div class="ml-64 flex flex-col min-h-screen transition-all duration-300">

            <header class="bg-white shadow-sm border-b border-slate-100 z-10 sticky top-0">
                <div class="w-full px-8 py-4 flex justify-between items-center">

                    <div class="text-xl font-bold text-[#1b2559]">
                        @if (isset($header))
                            {{ $header }}
                        @endif
                    </div>

                    <div class="flex items-center gap-3 bg-slate-50 px-4 py-1.5 rounded-full border border-slate-200 shadow-sm cursor-pointer hover:bg-slate-100 transition-colors">
                        <span class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</span>
                        <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm uppercase shadow-inner">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>

                </div>
            </header>

            <main class="flex-1 w-full p-8">
                {{ $slot }}
            </main>

        </div>

    </div>
</body>
</html>
