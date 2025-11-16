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
                <h1 class="text-3xl font-semibold">Two-Factor Authentication</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Add an extra layer of security to your account
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

            @if ($user->two_factor_enabled)
                <!-- 2FA Enabled -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 mb-6">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold mb-2">Two-Factor Authentication is Enabled</h2>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                Your account is protected with two-factor authentication.
                            </p>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">
                                Enabled on {{ $user->two_factor_confirmed_at->format('M d, Y \a\t H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a
                            href="{{ route('settings.two-factor.recovery-codes') }}"
                            class="px-4 py-2 bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#e3e3e0] dark:hover:bg-[#3E3E3A] font-medium rounded-sm transition-all"
                        >
                            View Recovery Codes
                        </a>
                        <button
                            onclick="document.getElementById('disable-2fa-modal').classList.remove('hidden')"
                            class="px-4 py-2 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900 rounded-sm hover:bg-red-50 dark:hover:bg-red-900/20 font-medium transition-all"
                        >
                            Disable 2FA
                        </button>
                    </div>
                </div>

                <!-- Disable 2FA Modal -->
                <div id="disable-2fa-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 max-w-md w-full mx-4">
                        <h3 class="text-lg font-semibold mb-4">Disable Two-Factor Authentication</h3>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                            Enter your password to disable two-factor authentication. This will make your account less secure.
                        </p>

                        <form method="POST" action="{{ route('settings.two-factor.disable') }}">
                            @csrf
                            @method('DELETE')

                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium mb-2">Password</label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                                >
                            </div>

                            <div class="flex gap-3">
                                <button
                                    type="submit"
                                    class="flex-1 px-4 py-2 bg-red-600 dark:bg-red-600 text-white hover:bg-red-700 dark:hover:bg-red-700 font-medium rounded-sm transition-all"
                                >
                                    Disable 2FA
                                </button>
                                <button
                                    type="button"
                                    onclick="document.getElementById('disable-2fa-modal').classList.add('hidden')"
                                    class="px-4 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium rounded-sm transition-all"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <!-- Enable 2FA -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold mb-2">Enable Two-Factor Authentication</h2>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to sign in.
                        </p>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900 rounded-sm mb-6">
                        <p class="text-sm text-blue-800 dark:text-blue-400 font-medium mb-2">How it works:</p>
                        <ul class="list-disc list-inside text-sm text-blue-800 dark:text-blue-400 space-y-1">
                            <li>Install an authenticator app (Google Authenticator, Authy, etc.)</li>
                            <li>Scan the QR code with your app</li>
                            <li>Enter the verification code to confirm</li>
                            <li>Save your recovery codes in a safe place</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('settings.two-factor.enable') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium mb-2">
                                Confirm Your Password <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                placeholder="Enter your current password"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                            >
                        </div>

                        <button
                            type="submit"
                            class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                        >
                            Enable Two-Factor Authentication
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
