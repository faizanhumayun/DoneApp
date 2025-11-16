@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-2xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Invite Guest</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Invite a guest to collaborate on specific tasks or discussions. Guests have limited access.
                </p>
            </div>

            @if ($context)
                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900 rounded-sm">
                    <p class="text-sm text-blue-800 dark:text-blue-400">
                        Inviting guest for task: <strong>{{ $context->task_number }} - {{ $context->title }}</strong>
                    </p>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900 rounded-sm">
                    <ul class="list-disc list-inside text-sm text-red-800 dark:text-red-400">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Invitation Form -->
            <form method="POST" action="{{ route('guests.store') }}">
                @csrf

                @if ($fromType && $fromId)
                    <input type="hidden" name="from_type" value="{{ $fromType }}">
                    <input type="hidden" name="from_id" value="{{ $fromId }}">
                @endif

                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name') }}"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name') }}"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                    </div>

                    <!-- Role Info -->
                    <div>
                        <label class="block text-sm font-medium mb-2">User Type</label>
                        <div class="px-4 py-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#706f6c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="font-medium">Guest Member</span>
                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">(Limited Access)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Message -->
                    <div>
                        <label for="personal_message" class="block text-sm font-medium mb-2">
                            Personal Message <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">(Optional)</span>
                        </label>
                        <textarea
                            id="personal_message"
                            name="personal_message"
                            rows="4"
                            placeholder="Add a personal message to the invitation email..."
                            class="w-full px-4 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >{{ old('personal_message') }}</textarea>
                    </div>

                    <!-- Guest Access Info -->
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900 rounded-sm">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-amber-800 dark:text-amber-400">
                                <p class="font-medium mb-1">Guest Access Limitations:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Can only view tasks they're assigned to or watching</li>
                                    <li>Cannot browse all projects, workflows, or users</li>
                                    <li>Cannot access company settings or billing</li>
                                    <li>Limited to specific tasks and discussions</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button
                            type="submit"
                            class="flex-1 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                        >
                            Send Invitation
                        </button>
                        <a
                            href="{{ route('guests.index') }}"
                            class="px-5 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium rounded-sm transition-all"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
