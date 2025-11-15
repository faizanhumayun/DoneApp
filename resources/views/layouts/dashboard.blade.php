<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Alpine.js Cloak Style -->
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Tailwind Configuration -->
        <script>
            window.tailwindConfig = {
                darkMode: 'class'
            }
        </script>
        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <!-- Theme Script -->
    <script>
        // Initialize theme before page renders to prevent flash
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"
      x-data="{
          theme: localStorage.getItem('theme') || 'light',
          setTheme(newTheme) {
              this.theme = newTheme;
              localStorage.setItem('theme', newTheme);
              if (newTheme === 'dark') {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }">
    <!-- Top Navigation Bar -->
    <nav class="fixed top-0 left-0 right-0 bg-white dark:bg-[#161615] border-b border-[#e3e3e0] dark:border-[#3E3E3A] z-50">
        <div class="w-full px-4 lg:px-6">
            <div class="flex items-center justify-between h-14">
                <!-- Left Group: Primary Navigation -->
                <div class="flex items-center gap-1">
                    <!-- Logo/Brand -->
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 text-lg font-semibold mr-4">
                        {{ config('app.name', 'Laravel') }}
                    </a>

                    <!-- Navigation Tabs -->
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('dashboard') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                        Home
                    </a>
                    <a href="#"
                       class="px-3 py-2 text-sm font-medium rounded-sm text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] transition-all">
                        Tasks
                    </a>
                    <a href="#"
                       class="px-3 py-2 text-sm font-medium rounded-sm text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] transition-all">
                        Conversations
                    </a>
                    <a href="{{ route('workflows.index') }}"
                       class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('workflows.*') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                        Workflows
                    </a>
                    <a href="#"
                       class="px-3 py-2 text-sm font-medium rounded-sm text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] transition-all">
                        Users
                    </a>
                    <a href="#"
                       class="px-3 py-2 text-sm font-medium rounded-sm text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] transition-all">
                        Guests
                    </a>

                    <!-- More Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            class="px-3 py-2 text-sm font-medium rounded-sm text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] transition-all flex items-center gap-1">
                            More
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-48 bg-white dark:bg-[#161615] rounded-sm shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] py-1"
                             x-cloak>
                            <a href="#" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                Settings
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                Reports
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                Analytics
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Group: Global Actions -->
                <div class="flex items-center gap-2">
                    <!-- Search Icon -->
                    <button class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>

                    <!-- Notifications Bell -->
                    <button class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <!-- Notification badge -->
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- Add New Button -->
                    <button class="flex items-center gap-2 px-3 py-1.5 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white rounded-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">Add New</span>
                    </button>

                    <!-- User Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 p-1 hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all">
                            <!-- User Avatar -->
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-semibold">
                                {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                            </div>
                            <!-- Caret -->
                            <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white dark:bg-[#161615] rounded-sm shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] py-1"
                             x-cloak>
                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                                <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ auth()->user()->full_name }}</p>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ auth()->user()->email }}</p>
                            </div>

                            <!-- Theme Selector -->
                            <div class="px-4 py-3 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                                <p class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">Theme</p>
                                <div class="flex gap-2">
                                    <button
                                        @click="setTheme('light')"
                                        :class="theme === 'light' ? 'bg-[#1b1b18] text-white' : 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#706f6c] dark:text-[#A1A09A]'"
                                        class="flex-1 px-3 py-1.5 rounded-sm text-xs font-medium transition-all flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        Light
                                    </button>
                                    <button
                                        @click="setTheme('dark')"
                                        :class="theme === 'dark' ? 'bg-[#eeeeec] text-[#1C1C1A]' : 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#706f6c] dark:text-[#A1A09A]'"
                                        class="flex-1 px-3 py-1.5 rounded-sm text-xs font-medium transition-all flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                        Dark
                                    </button>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                My Profile
                            </a>

                            <div class="border-t border-[#e3e3e0] dark:border-[#3E3E3A] mt-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area (with top padding to account for fixed nav) -->
    <main class="pt-14 min-h-screen">
        @yield('content')
    </main>

    <!-- Alpine.js - Load at end of body -->
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
