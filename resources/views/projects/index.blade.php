@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold mb-2">Projects</h1>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">
                        Manage your projects and track progress.
                    </p>
                </div>
                @php
                    $userRole = $company->users()->where('user_id', auth()->id())->first()->pivot->role;
                @endphp
                @if (in_array($userRole, ['owner', 'admin']))
                    <a href="{{ route('projects.create') }}"
                       class="flex items-center gap-2 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create Project
                    </a>
                @endif
            </div>

            <!-- Success Message -->
            @if (session('message'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('message') }}</p>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-[#fff2f2] dark:bg-[#1D0002] border border-red-200 dark:border-red-900 rounded-sm">
                    <ul class="text-sm text-[#F53003] dark:text-[#FF4433] space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Project Cards -->
            @if ($projects->isEmpty())
                <div class="text-center py-16">
                    <svg class="mx-auto w-16 h-16 text-[#706f6c] dark:text-[#A1A09A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">No projects yet</h3>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] mb-6">Get started by creating your first project.</p>
                    @if (in_array($userRole, ['owner', 'admin']))
                        <a href="{{ route('projects.create') }}"
                           class="inline-flex items-center gap-2 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create Project
                        </a>
                    @endif
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($projects as $project)
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:shadow-md transition-shadow">
                            <!-- Project Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <a href="{{ route('projects.show', $project) }}" class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-1 hover:text-blue-600 dark:hover:text-blue-400">
                                        {{ $project->name }}
                                    </a>
                                    @if ($project->description)
                                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                                            {{ Str::limit($project->description, 80) }}
                                        </p>
                                    @endif
                                </div>
                                <!-- Actions Dropdown -->
                                <div x-data="{ open: false }" class="relative">
                                    <button
                                        @click="open = !open"
                                        class="p-1 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-cloak
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#161615] rounded-sm shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] py-1 z-10">
                                        <a href="{{ route('projects.show', $project) }}"
                                           class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                            View
                                        </a>
                                        @if (in_array($userRole, ['owner', 'admin']))
                                            <a href="{{ route('projects.edit', $project) }}"
                                               class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                                Edit
                                            </a>
                                        @endif
                                        @if ($userRole === 'owner')
                                            <form method="POST" action="{{ route('projects.archive', $project) }}"
                                                  onsubmit="return confirm('Are you sure you want to archive this project?')"
                                                  class="block border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                                    Archive
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.')"
                                                  class="block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Project Meta -->
                            <div class="space-y-2 mt-4">
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-[#706f6c] dark:text-[#A1A09A]">Workflow:</span>
                                    <span class="text-[#1b1b18] dark:text-[#EDEDEC] font-medium">{{ $project->workflow->name }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span class="text-[#706f6c] dark:text-[#A1A09A]">Members:</span>
                                    <span class="text-[#1b1b18] dark:text-[#EDEDEC] font-medium">{{ $project->users->count() }}</span>
                                </div>
                                @if ($project->total_estimated_hours)
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-[#706f6c] dark:text-[#A1A09A]">Estimated Hours:</span>
                                        <span class="text-[#1b1b18] dark:text-[#EDEDEC] font-medium">{{ $project->total_estimated_hours }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
