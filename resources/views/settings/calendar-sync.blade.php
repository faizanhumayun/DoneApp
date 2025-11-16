@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Settings
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Calendar Sync</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Sync your tasks and deadlines with external calendars
                </p>
            </div>

            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 mb-6">
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold mb-2">Calendar Integration Coming Soon</h2>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] max-w-md mx-auto">
                        We're working on integrating with popular calendar services like Google Calendar, Outlook, and iCal. Soon you'll be able to sync your tasks and deadlines automatically.
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h3 class="text-lg font-semibold mb-4">Planned Features</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="font-medium text-sm">Two-way Sync</p>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Changes in your calendar will update tasks automatically</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="font-medium text-sm">Multiple Calendar Support</p>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Connect Google Calendar, Outlook, and Apple Calendar</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="font-medium text-sm">Customizable Sync</p>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Choose which projects and tasks to sync</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
