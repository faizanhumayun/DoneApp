@extends('layouts.signup')

@section('content')
    <div>
        <h2 class="text-2xl font-semibold mb-2">Welcome back</h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-6">Log in to your account</p>

        <div class="text-center py-8">
            <p class="text-[#706f6c] dark:text-[#A1A09A]">Login functionality to be implemented</p>
        </div>
    </div>
@endsection

@section('footer')
    Don't have an account?
    <a href="{{ route('signup.email') }}" class="text-[#1b1b18] dark:text-[#EDEDEC] underline underline-offset-4">Sign up</a>
@endsection
