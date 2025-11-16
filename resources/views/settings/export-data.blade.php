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
                <h1 class="text-3xl font-semibold">Export Data</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Download all your account data in CSV format
                </p>
            </div>

            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold mb-2">Download Your Data</h2>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                            Export all your data including users, projects, tasks, and discussions in a single CSV file.
                        </p>
                    </div>
                </div>

                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900 rounded-sm mb-6">
                    <p class="text-sm text-amber-800 dark:text-amber-400">
                        <strong>What's included in the CSV:</strong>
                    </p>
                    <ul class="list-disc list-inside text-sm text-amber-800 dark:text-amber-400 mt-2 space-y-1">
                        <li>Users section - All team members and guests with roles</li>
                        <li>Projects section - All projects with descriptions and statuses</li>
                        <li>Tasks section - All tasks with assignments, priorities, and due dates</li>
                        <li>Discussions section - All discussions with privacy settings</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('settings.export.download') }}">
                    @csrf
                    <button
                        type="submit"
                        class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                    >
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Data Export
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
