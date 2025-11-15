@extends('layouts.signup')

@section('content')
    <div>
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-semibold mb-2">Check your email</h2>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                We've sent a verification link to<br>
                <strong class="text-[#1b1b18] dark:text-[#EDEDEC]">{{ $email }}</strong>
            </p>
        </div>

        @if (session('resent'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                <p class="text-sm text-green-800 dark:text-green-400">Verification email sent! Please check your inbox.</p>
            </div>
        @endif

        <div class="space-y-4">
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                Please click the link in that email to continue setting up your account.
            </p>

            <div class="pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-3">Didn't receive the email?</p>
                <form method="POST" action="{{ route('signup.resend') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button
                        type="submit"
                        class="w-full px-5 py-2 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm transition-all"
                    >
                        Resend verification email
                    </button>
                </form>
            </div>

            <div class="pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <a
                    href="{{ route('signup.email') }}"
                    class="block w-full px-5 py-2 text-center border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm transition-all"
                >
                    Change email address
                </a>
            </div>
        </div>
    </div>
@endsection
