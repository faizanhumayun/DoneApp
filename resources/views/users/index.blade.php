@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold">Users</h1>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                        Manage all members of your team
                    </p>
                </div>
                <a
                    href="{{ route('signup.email') }}"
                    class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                >
                    Invite User
                </a>
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

            <!-- Search & Filter Bar -->
            <div class="mb-6 flex gap-4 items-center">
                <div class="flex-1">
                    <form method="GET" action="{{ route('users.index') }}" class="flex gap-4">
                        <input type="hidden" name="tab" value="{{ $tab }}">

                        <div class="flex-1">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search users by name or email..."
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                            >
                        </div>

                        <select
                            name="role"
                            class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                            <option value="all">All Roles</option>
                            <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Member</option>
                            <option value="guest" {{ request('role') === 'guest' ? 'selected' : '' }}>Guest</option>
                        </select>

                        <button
                            type="submit"
                            class="px-5 py-2 bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#e3e3e0] dark:hover:bg-[#3E3E3A] font-medium rounded-sm transition-all"
                        >
                            Filter
                        </button>

                        @if (request('search') || request('role') !== 'all')
                            <a
                                href="{{ route('users.index', ['tab' => $tab]) }}"
                                class="px-5 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium rounded-sm transition-all"
                            >
                                Clear
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                <nav class="flex gap-8">
                    <a
                        href="{{ route('users.index', ['tab' => 'active']) }}"
                        class="pb-4 px-1 border-b-2 font-medium text-sm transition-all {{ $tab === 'active' ? 'border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC]' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:border-[#706f6c] dark:hover:border-[#A1A09A]' }}"
                    >
                        Active <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400">{{ $activeCount }}</span>
                    </a>
                    <a
                        href="{{ route('users.index', ['tab' => 'inactive']) }}"
                        class="pb-4 px-1 border-b-2 font-medium text-sm transition-all {{ $tab === 'inactive' ? 'border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC]' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:border-[#706f6c] dark:hover:border-[#A1A09A]' }}"
                    >
                        Inactive <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-orange-100 dark:bg-orange-900/20 text-orange-800 dark:text-orange-400">{{ $inactiveCount }}</span>
                    </a>
                    <a
                        href="{{ route('users.index', ['tab' => 'archived']) }}"
                        class="pb-4 px-1 border-b-2 font-medium text-sm transition-all {{ $tab === 'archived' ? 'border-[#1b1b18] dark:border-[#EDEDEC] text-[#1b1b18] dark:text-[#EDEDEC]' : 'border-transparent text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] hover:border-[#706f6c] dark:hover:border-[#A1A09A]' }}"
                    >
                        Archived <span class="ml-2 px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-400">{{ $archivedCount }}</span>
                    </a>
                </nav>
            </div>

            <!-- User List -->
            @if ($users->isEmpty())
                <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-[#D6D6D6] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">No users found</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($users as $user)
                        @include('users.partials.user-card', ['user' => $user, 'tab' => $tab])
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Edit User Modal -->
    @include('users.partials.edit-modal')
@endsection

