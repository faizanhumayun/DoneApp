@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
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
            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Account Settings</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Manage your account name, logo, and email footer
                </p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
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

            <!-- Account Settings Form -->
            <form method="POST" action="{{ route('settings.account.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
                    <!-- Account Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">
                            Account Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $company->name) }}"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                    </div>

                    <!-- Account Logo -->
                    <div>
                        <label for="logo" class="block text-sm font-medium mb-2">
                            Account Logo
                        </label>

                        @if ($company->logo_path)
                            <div class="mb-4">
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-2">Current Logo:</p>
                                <img
                                    src="{{ Storage::url($company->logo_path) }}"
                                    alt="Company Logo"
                                    class="h-16 w-auto border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm p-2 bg-white dark:bg-[#161615]"
                                >
                            </div>
                        @endif

                        <input
                            type="file"
                            id="logo"
                            name="logo"
                            accept="image/*"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                            Accepted formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB.
                        </p>
                    </div>

                    <!-- Email Footer -->
                    <div>
                        <label for="email_footer" class="block text-sm font-medium mb-2">
                            Email Footer <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">(Optional)</span>
                        </label>
                        <textarea
                            id="email_footer"
                            name="email_footer"
                            rows="4"
                            placeholder="This footer will be added to all outgoing emails..."
                            class="w-full px-4 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >{{ old('email_footer', $company->email_footer) }}</textarea>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                            This footer will be included at the bottom of all emails sent from your account.
                        </p>
                    </div>

                    <!-- Info Box -->
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900 rounded-sm">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800 dark:text-blue-400">
                                <p class="font-medium mb-1">Email Branding</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Your company logo will appear in all emails sent from the system</li>
                                    <li>The email footer will be added to all email templates automatically</li>
                                    <li>Changes will apply to all future emails</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <button
                            type="submit"
                            class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                        >
                            Save Changes
                        </button>
                        <a
                            href="{{ route('settings.index') }}"
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
