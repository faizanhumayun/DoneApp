@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-6">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Settings
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Access Requests</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Manage guest access requests for your workspace
                </p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Tabs -->
            <div class="mb-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <div class="flex gap-6">
                    <a href="{{ route('settings.access-requests.index', ['status' => 'pending']) }}"
                       class="pb-3 px-1 border-b-2 transition-colors {{ $status === 'pending' ? 'border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC] font-semibold' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A]' }}">
                        Pending
                    </a>
                    <a href="{{ route('settings.access-requests.index', ['status' => 'approved']) }}"
                       class="pb-3 px-1 border-b-2 transition-colors {{ $status === 'approved' ? 'border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC] font-semibold' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A]' }}">
                        Approved
                    </a>
                    <a href="{{ route('settings.access-requests.index', ['status' => 'denied']) }}"
                       class="pb-3 px-1 border-b-2 transition-colors {{ $status === 'denied' ? 'border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC] font-semibold' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A]' }}">
                        Denied
                    </a>
                </div>
            </div>

            @if ($requests->isEmpty())
                <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-[#D6D6D6] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">No {{ $status }} requests found</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($requests as $request)
                        <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $request->full_name }}</h3>
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $request->email }}</p>
                                    @if ($request->message)
                                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ $request->message }}</p>
                                    @endif
                                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                                        Requested {{ $request->created_at->diffForHumans() }}
                                    </p>
                                    @if ($request->status !== 'pending')
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                            Reviewed by {{ $request->reviewer->full_name ?? 'Unknown' }} {{ $request->reviewed_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>

                                @if ($request->status === 'pending')
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('settings.access-requests.approve', $request) }}">
                                            @csrf
                                            <input type="hidden" name="send_invite" value="1">
                                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-sm hover:bg-green-700 text-sm">
                                                Approve & Invite
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('settings.access-requests.deny', $request) }}">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-sm hover:bg-red-700 text-sm">
                                                Deny
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
