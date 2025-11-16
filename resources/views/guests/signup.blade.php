<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Guest Sign Up - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#FAF9F7]">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo/Brand -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold">{{ config('app.name') }}</h1>
                <p class="text-[#706f6c] mt-2">Complete your guest account setup</p>
            </div>

            <!-- Invitation Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-blue-800">
                    <strong>{{ $invite->invitedBy->full_name }}</strong> from <strong>{{ $invite->company->name }}</strong> has invited you as a guest collaborator.
                </p>
                @if ($invite->personal_message)
                    <p class="text-sm text-blue-700 mt-2 italic">"{{ $invite->personal_message }}"</p>
                @endif
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-sm text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Signup Form -->
            <div class="bg-white rounded-lg shadow-sm border border-[#e3e3e0] p-6">
                <h2 class="text-xl font-semibold mb-6">Create Your Account</h2>

                <form method="POST" action="{{ route('guests.signup.submit', $invite->token) }}">
                    @csrf

                    <!-- First Name -->
                    <div class="mb-4">
                        <label for="first_name" class="block text-sm font-medium mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name', $invite->first_name) }}"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] rounded-sm focus:ring-2 focus:ring-[#1b1b18] focus:border-transparent"
                        >
                    </div>

                    <!-- Last Name -->
                    <div class="mb-4">
                        <label for="last_name" class="block text-sm font-medium mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name', $invite->last_name) }}"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] rounded-sm focus:ring-2 focus:ring-[#1b1b18] focus:border-transparent"
                        >
                    </div>

                    <!-- Email (Read-only) -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium mb-2">
                            Email Address
                        </label>
                        <input
                            type="email"
                            id="email"
                            value="{{ $invite->email }}"
                            readonly
                            class="w-full px-4 py-2 border border-[#e3e3e0] rounded-sm bg-[#f5f5f5] text-[#706f6c] cursor-not-allowed"
                        >
                        <p class="text-xs text-[#706f6c] mt-1">This email cannot be changed</p>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] rounded-sm focus:ring-2 focus:ring-[#1b1b18] focus:border-transparent"
                        >
                        <p class="text-xs text-[#706f6c] mt-1">Minimum 8 characters, must include a number and a symbol</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] rounded-sm focus:ring-2 focus:ring-[#1b1b18] focus:border-transparent"
                        >
                    </div>

                    <!-- About Me -->
                    <div class="mb-6">
                        <label for="about_yourself" class="block text-sm font-medium mb-2">
                            About Me <span class="text-xs text-[#706f6c]">(Optional)</span>
                        </label>
                        <textarea
                            id="about_yourself"
                            name="about_yourself"
                            rows="3"
                            placeholder="Tell us a bit about yourself..."
                            class="w-full px-4 py-3 border border-[#e3e3e0] rounded-sm focus:ring-2 focus:ring-[#1b1b18] focus:border-transparent"
                        >{{ old('about_yourself') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full px-5 py-3 bg-[#1b1b18] text-white hover:bg-black font-medium rounded-sm transition-all"
                    >
                        Create Account & Sign In
                    </button>
                </form>
            </div>

            <!-- Footer Info -->
            <p class="text-center text-sm text-[#706f6c] mt-6">
                Already have an account? <a href="{{ route('login') }}" class="text-[#1b1b18] hover:underline font-medium">Sign In</a>
            </p>
        </div>
    </div>
</body>
</html>
