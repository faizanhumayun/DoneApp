@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Settings
                </a>
            </div>

            <!-- Page Header -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-semibold">Logs</h1>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                        View login and email activity logs (6-month retention)
                    </p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex gap-6">
                    <a
                        href="{{ route('settings.logs', ['tab' => 'login']) }}"
                        class="pb-3 px-1 border-b-2 transition-colors {{ $tab === 'login' ? 'border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC] font-semibold' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}"
                    >
                        Login Log
                    </a>
                    <a
                        href="{{ route('settings.logs', ['tab' => 'email']) }}"
                        class="pb-3 px-1 border-b-2 transition-colors {{ $tab === 'email' ? 'border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC] font-semibold' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]' }}"
                    >
                        Email Log
                    </a>
                </div>
            </div>

            @if ($tab === 'login')
                <!-- Login Log Tab -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="p-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Login Activity</h2>
                        <button
                            disabled
                            class="px-4 py-2 text-sm text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm opacity-50 cursor-not-allowed"
                        >
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export CSV
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        @if ($loginLogs->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-[#D6D6D6] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-[#706f6c] dark:text-[#A1A09A] mb-2">No login logs available</p>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Login tracking will be implemented soon</p>
                            </div>
                        @else
                            <table class="w-full">
                                <thead class="bg-[#f5f5f5] dark:bg-[#0a0a0a]">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Login Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">IP Address</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Device</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Session Duration</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                                    @foreach ($loginLogs as $log)
                                        <tr class="hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $log->user->full_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $log->login_at->format('M d, Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $log->ip_address }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $log->device }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $log->duration }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @else
                <!-- Email Log Tab -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="p-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A] flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Email Deliverability</h2>
                        <button
                            disabled
                            class="px-4 py-2 text-sm text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm opacity-50 cursor-not-allowed"
                        >
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Export CSV
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        @if ($emailLogs->isEmpty())
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-[#D6D6D6] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-[#706f6c] dark:text-[#A1A09A] mb-2">No email logs available</p>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Email tracking will be implemented soon</p>
                            </div>
                        @else
                            <table class="w-full">
                                <thead class="bg-[#f5f5f5] dark:bg-[#0a0a0a]">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Recipient</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Subject</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Sent At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] uppercase tracking-wider">Type</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]">
                                    @foreach ($emailLogs as $log)
                                        <tr class="hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $log->recipient }}</td>
                                            <td class="px-6 py-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $log->subject }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $log->sent_at->format('M d, Y H:i') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $log->status === 'delivered' ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' }}">
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $log->type }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900 rounded-sm">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-amber-800 dark:text-amber-400">
                        <p class="font-medium mb-1">Data Retention Policy</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Log data is retained for 6 months</li>
                            <li>Older logs are automatically archived and available for CSV download</li>
                            <li>Login and email tracking features are coming soon</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
