@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('settings.two-factor.index') }}" class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Recovery Codes</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Use these codes to access your account if you lose your authentication device
                </p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 mb-6">
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900 rounded-sm mb-6">
                    <p class="text-sm text-amber-800 dark:text-amber-400">
                        <strong>Important:</strong> Each recovery code can only be used once. Store them securely.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    @foreach ($recoveryCodes as $code)
                        <div class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm text-center">
                            <code class="text-sm font-mono text-[#1b1b18] dark:text-[#EDEDEC]">{{ $code }}</code>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-3">
                    <button
                        onclick="copyRecoveryCodes()"
                        class="px-4 py-2 bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#e3e3e0] dark:hover:bg-[#3E3E3A] font-medium rounded-sm transition-all"
                    >
                        Copy All Codes
                    </button>
                    <button
                        onclick="document.getElementById('regenerate-modal').classList.remove('hidden')"
                        class="px-4 py-2 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900 rounded-sm hover:bg-amber-50 dark:hover:bg-amber-900/20 font-medium transition-all"
                    >
                        Regenerate Codes
                    </button>
                </div>
            </div>

            <!-- Regenerate Modal -->
            <div id="regenerate-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold mb-4">Regenerate Recovery Codes</h3>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                        This will invalidate all existing recovery codes. Enter your password to continue.
                    </p>

                    <form method="POST" action="{{ route('settings.two-factor.regenerate-codes') }}">
                        @csrf

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
                                class="flex-1 px-4 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                            >
                                Regenerate
                            </button>
                            <button
                                type="button"
                                onclick="document.getElementById('regenerate-modal').classList.add('hidden')"
                                class="px-4 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium rounded-sm transition-all"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function copyRecoveryCodes() {
            const codes = @json($recoveryCodes);
            const text = codes.join('\n');

            navigator.clipboard.writeText(text).then(() => {
                alert('Recovery codes copied to clipboard!');
            });
        }
    </script>
    @endpush
@endsection
