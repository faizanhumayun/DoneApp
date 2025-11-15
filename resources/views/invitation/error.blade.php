@extends('layouts.signup')

@section('content')
    <div class="text-center">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full mb-4">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-semibold mb-2">{{ $title }}</h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ $message }}</p>
            @if($suggestion)
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $suggestion }}</p>
            @endif
        </div>

        @if($showLoginLink ?? false)
            <a
                href="{{ route('login') }}"
                class="inline-block px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
            >
                Log In
            </a>
        @else
            <a
                href="{{ route('signup.email') }}"
                class="inline-block px-5 py-2 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm transition-all"
            >
                Go to Sign Up
            </a>
        @endif
    </div>
@endsection
