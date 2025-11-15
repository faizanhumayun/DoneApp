<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]">
    <div class="min-h-screen p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-semibold mb-2">Welcome to {{ config('app.name') }}!</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    Hello, {{ auth()->user()->full_name }}!
                </p>
            </div>

            @if (session('welcome'))
                <div class="mb-6 p-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-lg">
                    <h2 class="text-xl font-semibold mb-2 text-green-800 dark:text-green-400">
                        🎉 Your account is ready!
                    </h2>
                    <p class="text-green-700 dark:text-green-300">
                        You've successfully completed the signup process. Start exploring and make the most of your account.
                    </p>
                </div>
            @endif

            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-lg p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h2 class="text-xl font-semibold mb-4">Your Profile</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Name</dt>
                        <dd class="mt-1">{{ auth()->user()->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Email</dt>
                        <dd class="mt-1">{{ auth()->user()->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Timezone</dt>
                        <dd class="mt-1">{{ auth()->user()->timezone }}</dd>
                    </div>
                    @if (auth()->user()->companies->isNotEmpty())
                        <div>
                            <dt class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Company</dt>
                            <dd class="mt-1">{{ auth()->user()->companies->first()->name }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-6 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="px-5 py-2 border border-[#19140035] dark:border-[#3E3E3A] hover:border-red-500 dark:hover:border-red-500 text-[#1b1b18] dark:text-[#EDEDEC] hover:text-red-500 rounded-sm transition-all"
                        >
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
