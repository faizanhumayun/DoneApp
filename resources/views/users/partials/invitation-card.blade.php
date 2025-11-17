@php
    $isExpired = $invitation->isTokenExpired();
    $expiresAt = $invitation->invite_token_expires_at;
    $sentAt = $invitation->created_at;
@endphp

<div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 hover:border-[#2E8AF7] hover:shadow-md transition-all" x-data="{ openMenu: false }">
    <div class="flex items-center justify-between">
        <!-- Left side: Invitation info -->
        <div class="flex items-center gap-4 flex-1">
            <!-- Icon -->
            <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <!-- Invitation details -->
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $invitation->invited_email }}</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $isExpired ? 'bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-400' : 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400' }}">
                        {{ $isExpired ? 'Expired' : 'Pending' }}
                    </span>
                </div>
                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    <p>
                        Invited by {{ $invitation->invitedBy->full_name }} &middot;
                        {{ $sentAt->diffForHumans() }}
                    </p>
                    <p class="text-xs mt-1">
                        @if ($isExpired)
                            Expired {{ $expiresAt->diffForHumans() }}
                        @else
                            Expires {{ $expiresAt->diffForHumans() }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Right side: Actions -->
        <div class="flex items-center gap-2">
            <!-- Resend Button -->
            <form method="POST" action="{{ route('users.invitations.resend', $invitation) }}">
                @csrf
                <button
                    type="submit"
                    class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all"
                    title="Resend invitation"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </form>

            <!-- More Menu -->
            <div class="relative">
                <button
                    @click="openMenu = !openMenu"
                    class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all"
                    title="More actions"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div
                    x-show="openMenu"
                    @click.away="openMenu = false"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#161615] rounded-sm shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] py-1 z-10"
                    x-cloak
                >
                    <form method="POST" action="{{ route('users.invitations.cancel', $invitation) }}">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            onclick="return confirm('Cancel invitation to {{ $invitation->invited_email }}?')"
                            class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]"
                        >
                            Cancel Invitation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
