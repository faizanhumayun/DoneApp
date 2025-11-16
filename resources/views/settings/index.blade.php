@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Settings</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Manage your account settings and preferences
                </p>
            </div>

            <!-- Settings Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Account Settings -->
                <a href="{{ route('settings.account') }}" class="block">
                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:border-[#2E8AF7] transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold">Account Settings</h2>
                        </div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Manage account name, logo, and email footer
                        </p>
                    </div>
                </a>

                <!-- Billing Information -->
                <a href="{{ route('settings.billing') }}" class="block">
                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:border-[#2E8AF7] transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold">Billing Information</h2>
                        </div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            View and manage your billing details
                        </p>
                    </div>
                </a>

                <!-- Two-Factor Authentication -->
                <a href="{{ route('settings.two-factor.index') }}" class="block">
                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:border-[#2E8AF7] transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold">Two-Factor Authentication</h2>
                        </div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Add an extra layer of security
                        </p>
                    </div>
                </a>

                <!-- Export Data -->
                <a href="{{ route('settings.export') }}" class="block">
                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:border-[#2E8AF7] transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold">Export Data</h2>
                        </div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Export all your data in various formats
                        </p>
                    </div>
                </a>

                <!-- Calendar Sync -->
                <a href="{{ route('settings.calendar-sync') }}" class="block">
                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:border-[#2E8AF7] transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold">Calendar Sync</h2>
                        </div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Sync tasks with your calendar
                        </p>
                    </div>
                </a>

                <!-- Logs -->
                <a href="{{ route('settings.logs') }}" class="block">
                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:border-[#2E8AF7] transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold">Logs</h2>
                        </div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            View login and email activity logs
                        </p>
                    </div>
                </a>

                <!-- Access Request -->
                <a href="{{ route('settings.access-requests.index') }}" class="block">
                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:border-[#2E8AF7] transition-all">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-semibold">Access Requests</h2>
                        </div>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Manage guest access requests
                        </p>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
