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

    <!-- Theme Script - Must run BEFORE anything else -->
    <script>
        // Initialize theme before page renders to prevent flash
        // Default to dark theme
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'dark';
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            // Save default if not set
            if (!localStorage.getItem('theme')) {
                localStorage.setItem('theme', 'dark');
            }
        })();
    </script>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <!-- Tailwind Configuration - MUST be before Tailwind loads -->
        <script>
            tailwind = { config: { darkMode: 'class' } };
        </script>
        <!-- Tailwind CSS CDN -->
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    @stack('styles')
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]"
      x-data="{
          theme: localStorage.getItem('theme') || 'dark',
          setTheme(newTheme) {
              console.log('Setting theme to:', newTheme);
              this.theme = newTheme;
              localStorage.setItem('theme', newTheme);
              if (newTheme === 'dark') {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
              console.log('Theme set. HTML classes:', document.documentElement.className);
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
                    @if (auth()->user()->isGuest())
                        <!-- Guest Navigation -->
                        <a href="{{ route('guests.dashboard') }}"
                           class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('guests.dashboard') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                            Home
                        </a>
                        <a href="{{ route('tasks.index') }}"
                           class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('tasks.*') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                            My Tasks
                        </a>
                        <a href="{{ route('discussions.index') }}"
                           class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('discussions.*') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                            Discussions
                        </a>
                    @else
                        <!-- Member/Admin Navigation -->
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('dashboard') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                            Home
                        </a>
                        <a href="{{ route('workspace') }}"
                           class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('workspace') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                            Workspace
                        </a>
                        <a href="{{ route('tasks.index') }}"
                           class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('tasks.*') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                            Tasks
                        </a>
                        <a href="{{ route('discussions.index') }}"
                           class="px-3 py-2 text-sm font-medium rounded-sm {{ request()->routeIs('discussions.*') ? 'bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]' }} transition-all">
                            Discussions
                        </a>
                    @endif

                    <!-- More Dropdown (Not visible to guests) -->
                    @if (!auth()->user()->isGuest())
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
                                @php
                                    $userRole = auth()->user()->getCompanyRole();
                                    $isOwner = $userRole === 'owner';
                                    $isOwnerOrAdmin = in_array($userRole, ['owner', 'admin']);
                                @endphp

                                @if ($isOwner)
                                    <a href="{{ route('workflows.index') }}" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                        Workflows
                                    </a>
                                @endif

                                @if ($isOwnerOrAdmin)
                                    <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                        Users
                                    </a>
                                @endif

                                @if ($isOwner)
                                    <a href="{{ route('guests.index') }}" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                        Guests
                                    </a>
                                    <div class="border-t border-[#e3e3e0] dark:border-[#3E3E3A] my-1"></div>
                                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                        Settings
                                    </a>
                                @endif
                                <a href="#" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                    Reports
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                    Analytics
                                </a>
                            </div>
                        </div>
                    @endif
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
                    <button @click="$dispatch('toggle-notifications')" class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all relative" x-data="{ count: 0 }" x-init="$watch('$store.notifications', value => { count = value ? value.unread_count : 0 })">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <!-- Notification badge with count -->
                        <span x-show="count > 0" x-text="count > 99 ? '99+' : count" class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 flex items-center justify-center bg-red-500 text-white text-xs font-medium rounded-full" x-cloak></span>
                    </button>

                    <!-- Add New Button (Not visible to guests) -->
                    @if (!auth()->user()->isGuest())
                        <button class="flex items-center gap-2 px-3 py-1.5 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white rounded-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Add New</span>
                        </button>
                    @endif

                    <!-- User Menu -->
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 p-1 hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all">
                            <!-- User Avatar -->
                            <img
                                src="{{ auth()->user()->avatar_url }}"
                                alt="{{ auth()->user()->full_name }}"
                                class="w-8 h-8 rounded-full object-cover border-2 border-[#e3e3e0] dark:border-[#3E3E3A]"
                            >
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

    <!-- Notification Drawer -->
    <div x-data="notificationDrawer()"
         @toggle-notifications.window="toggleDrawer()"
         x-show="isOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-hidden"
         style="display: none;">
        <!-- Overlay -->
        <div x-show="isOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="closeDrawer()"
             class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Drawer -->
        <div x-show="isOpen"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="absolute right-0 top-0 h-full w-full sm:w-96 bg-white dark:bg-[#161615] shadow-xl flex flex-col">

            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Notifications</h2>
                <button @click="closeDrawer()" class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <button @click="activeTab = 'workspace'" :class="activeTab === 'workspace' ? 'border-b-2 border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'" class="flex-1 px-4 py-3 text-xs font-medium transition-all">
                    Workspace
                </button>
                <button @click="activeTab = 'mentions'" :class="activeTab === 'mentions' ? 'border-b-2 border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'" class="flex-1 px-4 py-3 text-xs font-medium transition-all">
                    Mentions
                </button>
                <button @click="activeTab = 'conversations'" :class="activeTab === 'conversations' ? 'border-b-2 border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'" class="flex-1 px-4 py-3 text-xs font-medium transition-all">
                    Conversations
                </button>
                <button @click="activeTab = 'invites'" :class="activeTab === 'invites' ? 'border-b-2 border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC]' : 'text-[#706f6c] dark:text-[#A1A09A]'" class="flex-1 px-4 py-3 text-xs font-medium transition-all">
                    Invites
                </button>
            </div>

            <!-- Notification List -->
            <div class="flex-1 overflow-y-auto p-4">
                <!-- Workspace Notifications -->
                <div x-show="activeTab === 'workspace'" class="space-y-3">
                    <template x-if="!loading && notifications.workspace && notifications.workspace.length === 0">
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] text-center py-8">No workspace notifications</p>
                    </template>
                    <template x-for="notification in notifications.workspace" :key="notification.id">
                        <div @click="handleNotificationClick(notification)" class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm cursor-pointer hover:bg-[#e3e3e0] dark:hover:bg-[#1C1C1A] transition-all" :class="!notification.is_read ? 'border-l-4 border-blue-500' : ''">
                            <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]" x-text="notification.title"></h4>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1" x-text="notification.message"></p>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]" x-text="formatDate(notification.created_at)"></span>
                        </div>
                    </template>
                </div>

                <!-- Mentions Notifications -->
                <div x-show="activeTab === 'mentions'" class="space-y-3">
                    <template x-if="!loading && notifications.mentions && notifications.mentions.length === 0">
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] text-center py-8">No mention notifications</p>
                    </template>
                    <template x-for="notification in notifications.mentions" :key="notification.id">
                        <div @click="handleNotificationClick(notification)" class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm cursor-pointer hover:bg-[#e3e3e0] dark:hover:bg-[#1C1C1A] transition-all" :class="!notification.is_read ? 'border-l-4 border-blue-500' : ''">
                            <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]" x-text="notification.title"></h4>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1" x-text="notification.message"></p>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]" x-text="formatDate(notification.created_at)"></span>
                        </div>
                    </template>
                </div>

                <!-- Conversation Notifications -->
                <div x-show="activeTab === 'conversations'" class="space-y-3">
                    <template x-if="!loading && notifications.conversations && notifications.conversations.length === 0">
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] text-center py-8">No conversation notifications</p>
                    </template>
                    <template x-for="notification in notifications.conversations" :key="notification.id">
                        <div @click="handleNotificationClick(notification)" class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm cursor-pointer hover:bg-[#e3e3e0] dark:hover:bg-[#1C1C1A] transition-all" :class="!notification.is_read ? 'border-l-4 border-blue-500' : ''">
                            <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]" x-text="notification.title"></h4>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1" x-text="notification.message"></p>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]" x-text="formatDate(notification.created_at)"></span>
                        </div>
                    </template>
                </div>

                <!-- Invite Notifications -->
                <div x-show="activeTab === 'invites'" class="space-y-3">
                    <template x-if="!loading && notifications.invites && notifications.invites.length === 0">
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] text-center py-8">No invite notifications</p>
                    </template>
                    <template x-for="notification in notifications.invites" :key="notification.id">
                        <div @click="handleNotificationClick(notification)" class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm cursor-pointer hover:bg-[#e3e3e0] dark:hover:bg-[#1C1C1A] transition-all" :class="!notification.is_read ? 'border-l-4 border-blue-500' : ''">
                            <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]" x-text="notification.title"></h4>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1" x-text="notification.message"></p>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]" x-text="formatDate(notification.created_at)"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="p-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <button @click="markAllAsRead()" class="w-full px-4 py-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all">
                    Mark All as Read
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Area (with top padding to account for fixed nav) -->
    <main class="pt-14 min-h-screen">
        @yield('content')
    </main>

    <!-- Alpine.js - Load at end of body -->
    <script src="//unpkg.com/alpinejs" defer></script>

    <!-- Theme Toggle Helper - Runs after Alpine loads -->
    <script>
        document.addEventListener('alpine:init', () => {
            console.log('Alpine initialized');
            // Ensure theme is applied on page load
            const currentTheme = localStorage.getItem('theme') || 'dark';
            console.log('Current theme on load:', currentTheme);
            if (currentTheme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });

        // Fallback: Also run when DOM is fully loaded
        window.addEventListener('DOMContentLoaded', () => {
            const currentTheme = localStorage.getItem('theme') || 'dark';
            console.log('DOMContentLoaded - Current theme:', currentTheme);
            console.log('HTML element classes:', document.documentElement.className);
        });
    </script>

    <!-- Session Timeout Warning Modal -->
    <div id="sessionTimeoutModal"
         class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center"
         x-data="{ show: false }"
         x-show="show"
         x-cloak>
        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 max-w-md w-full mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="flex-shrink-0">
                    <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Session Expiring Soon</h3>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">You will be logged out in <span id="countdown" class="font-bold text-[#1b1b18] dark:text-[#EDEDEC]">60</span> seconds</p>
                </div>
            </div>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-6">
                Your session is about to expire due to inactivity. Click "Stay Logged In" to continue your session.
            </p>
            <div class="flex gap-3">
                <button
                    onclick="extendSession()"
                    class="flex-1 px-4 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                    Stay Logged In
                </button>
                <button
                    onclick="logoutNow()"
                    class="flex-1 px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] font-medium rounded-sm transition-all">
                    Logout Now
                </button>
            </div>
        </div>
    </div>

    <!-- Idle Session Timeout Script -->
    <script>
        (function() {
            // Configuration
            const IDLE_TIMEOUT = 20 * 60 * 1000; // 20 minutes in milliseconds
            const WARNING_TIME = 19 * 60 * 1000; // 19 minutes - show warning 1 minute before timeout

            let idleTimer = null;
            let warningTimer = null;
            let countdownInterval = null;
            let warningShown = false;

            // Events that indicate user activity
            const activityEvents = [
                'mousedown',
                'mousemove',
                'keypress',
                'scroll',
                'touchstart',
                'click'
            ];

            // Show warning modal
            function showWarning() {
                if (warningShown) return;

                warningShown = true;
                const modal = document.getElementById('sessionTimeoutModal');
                modal.classList.remove('hidden');

                // Start countdown
                let secondsLeft = 60;
                const countdownElement = document.getElementById('countdown');

                countdownInterval = setInterval(() => {
                    secondsLeft--;
                    countdownElement.textContent = secondsLeft;

                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                        logoutNow();
                    }
                }, 1000);
            }

            // Hide warning modal
            function hideWarning() {
                warningShown = false;
                const modal = document.getElementById('sessionTimeoutModal');
                modal.classList.add('hidden');

                if (countdownInterval) {
                    clearInterval(countdownInterval);
                    countdownInterval = null;
                }
            }

            // Extend session (called when user clicks "Stay Logged In")
            window.extendSession = function() {
                hideWarning();
                resetTimers();
            };

            // Logout immediately
            window.logoutNow = function() {
                // Create a form and submit to logout route
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('logout') }}';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            };

            // Reset all timers
            function resetTimers() {
                // Clear existing timers
                if (idleTimer) clearTimeout(idleTimer);
                if (warningTimer) clearTimeout(warningTimer);
                if (countdownInterval) clearInterval(countdownInterval);

                // Hide warning if shown
                if (warningShown) {
                    hideWarning();
                }

                // Set warning timer (19 minutes)
                warningTimer = setTimeout(() => {
                    showWarning();
                }, WARNING_TIME);

                // Set idle timer (20 minutes)
                idleTimer = setTimeout(() => {
                    logoutNow();
                }, IDLE_TIMEOUT);
            }

            // Setup activity listeners
            function setupActivityListeners() {
                activityEvents.forEach(event => {
                    document.addEventListener(event, resetTimers, true);
                });
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', () => {
                setupActivityListeners();
                resetTimers();
                console.log('Session timeout initialized: 20 minutes idle timeout');
            });
        })();
    </script>

    <!-- Notification Drawer JavaScript -->
    <script>
        function notificationDrawer() {
            return {
                isOpen: false,
                activeTab: 'workspace',
                loading: false,
                notifications: {
                    workspace: [],
                    mentions: [],
                    conversations: [],
                    invites: [],
                    unread_count: 0
                },

                init() {
                    // Fetch notifications on load
                    this.fetchNotifications();

                    // Setup Alpine store for global access
                    Alpine.store('notifications', this.notifications);

                    // Refresh notifications every 60 seconds
                    setInterval(() => {
                        this.fetchNotifications();
                    }, 60000);
                },

                toggleDrawer() {
                    this.isOpen = !this.isOpen;
                    if (this.isOpen) {
                        this.fetchNotifications();
                    }
                },

                closeDrawer() {
                    this.isOpen = false;
                },

                async fetchNotifications() {
                    try {
                        this.loading = true;
                        const response = await fetch('/notifications', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const data = await response.json();
                            this.notifications = data;
                            Alpine.store('notifications', data);
                        }
                    } catch (error) {
                        console.error('Failed to fetch notifications:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async handleNotificationClick(notification) {
                    // Immediately remove notification from list (optimistic update)
                    this.removeNotificationFromList(notification);

                    // Mark as read in background
                    this.markAsRead(notification.id);

                    // Navigate to the link if provided
                    if (notification.data && notification.data.link) {
                        window.location.href = notification.data.link;
                    }
                },

                removeNotificationFromList(notification) {
                    const type = notification.type;

                    // Remove from the specific type array
                    if (type === 'workspace') {
                        this.notifications.workspace = this.notifications.workspace.filter(n => n.id !== notification.id);
                    } else if (type === 'mention') {
                        this.notifications.mentions = this.notifications.mentions.filter(n => n.id !== notification.id);
                    } else if (type === 'conversation') {
                        this.notifications.conversations = this.notifications.conversations.filter(n => n.id !== notification.id);
                    } else if (type === 'invite') {
                        this.notifications.invites = this.notifications.invites.filter(n => n.id !== notification.id);
                    }

                    // Update unread count
                    this.notifications.unread_count = Math.max(0, this.notifications.unread_count - 1);

                    // Update Alpine store
                    Alpine.store('notifications', this.notifications);
                },

                async markAsRead(notificationId) {
                    try {
                        await fetch(`/notifications/${notificationId}/read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                    } catch (error) {
                        console.error('Failed to mark notification as read:', error);
                        // Silently fail - the notification was already removed from the UI
                    }
                },

                async markAllAsRead() {
                    try {
                        // Immediately clear all notifications (optimistic update)
                        this.notifications.workspace = [];
                        this.notifications.mentions = [];
                        this.notifications.conversations = [];
                        this.notifications.invites = [];
                        this.notifications.unread_count = 0;

                        // Update Alpine store
                        Alpine.store('notifications', this.notifications);

                        // Mark all as read in background
                        await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                    } catch (error) {
                        console.error('Failed to mark all notifications as read:', error);
                        // Silently fail - notifications were already cleared from the UI
                    }
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diff = now - date;
                    const seconds = Math.floor(diff / 1000);
                    const minutes = Math.floor(seconds / 60);
                    const hours = Math.floor(minutes / 60);
                    const days = Math.floor(hours / 24);

                    if (seconds < 60) return 'Just now';
                    if (minutes < 60) return `${minutes}m ago`;
                    if (hours < 24) return `${hours}h ago`;
                    if (days < 7) return `${days}d ago`;

                    return date.toLocaleDateString();
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
