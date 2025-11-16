@php
    $userCompany = $user->companies->first();
    $role = $userCompany->pivot->role ?? 'member';

    $roleBadgeColors = [
        'owner' => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-400',
        'admin' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400',
        'member' => 'bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-400',
        'guest' => 'bg-amber-100 dark:bg-amber-900/20 text-amber-800 dark:text-amber-400',
    ];

    $statusBadgeColors = [
        'active' => 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400',
        'inactive' => 'bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-400',
        'archived' => 'bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-400',
    ];
@endphp

<div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 hover:border-[#2E8AF7] hover:shadow-md transition-all" x-data="{ openMenu: false }">
    <div class="flex items-center justify-between">
        <!-- Left side: User info -->
        <div class="flex items-center gap-4 flex-1">
            <!-- Avatar -->
            <img
                src="{{ $user->avatar_url }}"
                alt="{{ $user->full_name }}"
                class="w-12 h-12 rounded-full object-cover border-2 border-[#e3e3e0] dark:border-[#3E3E3A]"
            >

            <!-- User details -->
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $user->full_name }}</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $roleBadgeColors[$role] ?? $roleBadgeColors['member'] }}">
                        {{ ucfirst($role) }}
                    </span>
                    @if ($tab !== 'active')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusBadgeColors[$user->status] ?? $statusBadgeColors['active'] }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $user->email }}</p>
                @if ($user->about_yourself)
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ Str::limit($user->about_yourself, 80) }}</p>
                @endif
            </div>
        </div>

        <!-- Right side: Actions -->
        <div class="flex items-center gap-2">
            <!-- Edit Button -->
            <button
                @click="$dispatch('open-edit-modal', {
                    id: {{ $user->id }},
                    first_name: '{{ $user->first_name }}',
                    last_name: '{{ $user->last_name }}',
                    email: '{{ $user->email }}',
                    role: '{{ $role }}',
                    status: '{{ $user->status }}',
                    about_yourself: {{ json_encode($user->about_yourself) }}
                })"
                class="p-2 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all"
                title="Edit user"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </button>

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
                    @if ($tab !== 'archived')
                        <form method="POST" action="{{ route('users.archive', $user) }}">
                            @csrf
                            <button
                                type="submit"
                                onclick="return confirm('Archive {{ $user->full_name }}? They will no longer be able to log in.')"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]"
                            >
                                Archive User
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('users.restore', $user) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]"
                            >
                                Restore User
                            </button>
                        </form>
                    @endif
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
