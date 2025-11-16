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

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Confirm Two-Factor Authentication</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Scan the QR code and enter a verification code
                </p>
            </div>

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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- QR Code -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                    <h2 class="text-lg font-semibold mb-4">Step 1: Scan QR Code</h2>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                        Use your authenticator app to scan this QR code
                    </p>

                    <div class="flex justify-center mb-4">
                        <div class="p-4 bg-white rounded-lg border-2 border-[#e3e3e0]">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="w-48 h-48">
                        </div>
                    </div>

                    <div class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm">
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Manual entry key:</p>
                        <code class="text-sm font-mono text-[#1b1b18] dark:text-[#EDEDEC] break-all">{{ $secret }}</code>
                    </div>
                </div>

                <!-- Verification -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                    <h2 class="text-lg font-semibold mb-4">Step 2: Verify Code</h2>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                        Enter the 6-digit code from your authenticator app
                    </p>

                    <form method="POST" action="{{ route('settings.two-factor.confirm') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="code" class="block text-sm font-medium mb-2">
                                Verification Code <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="code"
                                name="code"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                required
                                placeholder="000000"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] text-2xl text-center tracking-widest font-mono focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                        >
                            Verify and Enable 2FA
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recovery Codes -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold mb-4">Step 3: Save Recovery Codes</h2>
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900 rounded-sm mb-4">
                    <p class="text-sm text-amber-800 dark:text-amber-400">
                        <strong>Important:</strong> Store these recovery codes in a safe place. Each code can be used once if you lose access to your authenticator app.
                    </p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ($recoveryCodes as $code)
                        <div class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm text-center">
                            <code class="text-sm font-mono text-[#1b1b18] dark:text-[#EDEDEC]">{{ $code }}</code>
                        </div>
                    @endforeach
                </div>

                <button
                    onclick="copyRecoveryCodes()"
                    class="mt-4 px-4 py-2 bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#e3e3e0] dark:hover:bg-[#3E3E3A] font-medium rounded-sm transition-all"
                >
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Copy All Codes
                </button>
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
