@extends('layouts.signup')

@section('content')
    <div>
        <h2 class="text-2xl font-semibold mb-2">Create your account</h2>
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-6">Enter your work email to get started</p>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-[#fff2f2] dark:bg-[#1D0002] border border-red-200 dark:border-red-900 rounded-sm">
                <p class="text-sm text-[#F53003] dark:text-[#FF4433]">{{ $errors->first() }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('signup.email.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="work_email" class="block text-sm font-medium mb-2">Work Email</label>
                <input
                    type="email"
                    id="work_email"
                    name="work_email"
                    value="{{ old('work_email') }}"
                    required
                    autofocus
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('work_email') border-red-500 @enderror"
                    placeholder="you@company.com"
                >
                @error('work_email')
                    <p class="mt-1 text-sm text-[#F53003] dark:text-[#FF4433]">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
            >
                Continue
            </button>
        </form>
    </div>
@endsection

@section('footer')
    Already have an account?
    <a href="{{ route('login') }}" class="text-[#1b1b18] dark:text-[#EDEDEC] underline underline-offset-4">Log in</a>
@endsection
