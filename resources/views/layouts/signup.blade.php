<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Sign Up' }} - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Same Tailwind CSS from welcome.blade.php */
                @import url('{{ asset('build/app.css') }}');
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] flex p-6 lg:p-8 items-center justify-center min-h-screen">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <a href="{{ url('/') }}" class="inline-block mb-4">
                    <h1 class="text-2xl font-semibold">{{ config('app.name', 'Laravel') }}</h1>
                </a>
            </div>

            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-lg p-8 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                @yield('content')
            </div>

            <div class="mt-6 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                @yield('footer')
            </div>
        </div>
    </body>
</html>
