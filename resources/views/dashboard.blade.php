@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Welcome Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold mb-2">Welcome back, {{ auth()->user()->first_name }}!</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    Here's what's happening with your projects today.
                </p>
            </div>

            <!-- Welcome Message (if first time) -->
            @if (session('welcome'))
                <div class="mb-6 p-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-lg">
                    <h2 class="text-xl font-semibold mb-2 text-green-800 dark:text-green-400">
                        🎉 Your account is ready!
                    </h2>
                    <p class="text-green-700 dark:text-green-300">
                        You've successfully completed the setup. Start exploring and make the most of your account.
                    </p>
                </div>
            @endif

            <!-- Success Message -->
            @if (session('message'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('message') }}</p>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Total Tasks</p>
                            <p class="text-3xl font-semibold mt-2">0</p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Active Workflows</p>
                            <p class="text-3xl font-semibold mt-2">0</p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Team Members</p>
                            <p class="text-3xl font-semibold mt-2">{{ auth()->user()->companies->first()->users->count() ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h2 class="text-xl font-semibold mb-4">Your Profile</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                        <div>
                            <dt class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Role</dt>
                            <dd class="mt-1 capitalize">{{ auth()->user()->companies->first()->pivot->role }}</dd>
                        </div>
                    @endif
                    @if(auth()->user()->about_yourself)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">About</dt>
                            <dd class="mt-1">{{ auth()->user()->about_yourself }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
@endsection
