@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                    <a href="{{ route('projects.index') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">Projects</a>
                    <span>/</span>
                    <span class="text-[#1b1b18] dark:text-[#EDEDEC]">{{ $project->name }}</span>
                </div>

                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h1 class="text-3xl font-semibold mb-2">{{ $project->name }}</h1>
                        @if ($project->description)
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $project->description }}
                            </p>
                        @endif
                    </div>

                    @if (in_array($userRole, ['owner', 'admin']))
                        <a href="{{ route('projects.edit', $project) }}"
                           class="flex items-center gap-2 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Project
                        </a>
                    @endif
                </div>
            </div>

            <!-- Success Message -->
            @if (session('message'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('message') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content - Tasks Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Workflow Statuses -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Workflow Stages</h2>
                            <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $project->workflow->name }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($project->workflow->statuses as $status)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium"
                                      style="background-color: {{ $status->color }}; color: {{ $status->text_color }}">
                                    {{ $status->name }}
                                    @if (!$status->is_active)
                                        <span class="text-xs opacity-75">(Inactive)</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tasks Section - Empty State -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Tasks</h2>
                            <button
                                class="flex items-center gap-2 px-4 py-2 bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#e3e3e0] dark:hover:bg-[#1C1C1A] rounded-sm transition-all"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Task
                            </button>
                        </div>

                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <svg class="mx-auto w-16 h-16 text-[#706f6c] dark:text-[#A1A09A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <h3 class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">No tasks yet</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A] mb-6">Get started by creating your first task for this project.</p>
                            <button
                                class="inline-flex items-center gap-2 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Create First Task
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Project Details -->
                <div class="space-y-6">
                    <!-- Project Information -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                        <h2 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Project Details</h2>

                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Created By</p>
                                <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $project->creator->first_name }} {{ $project->creator->last_name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Created</p>
                                <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $project->created_at->format('M d, Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Last Updated</p>
                                <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $project->updated_at->diffForHumans() }}
                                </p>
                            </div>

                            @if ($project->total_estimated_hours)
                                <div>
                                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Estimated Hours</p>
                                    <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                        {{ $project->total_estimated_hours }} hours
                                    </p>
                                </div>
                            @endif

                            @if (!in_array($userRole, ['member']))
                                @if ($project->estimated_cost)
                                    <div>
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Estimated Cost</p>
                                        <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                            ${{ number_format($project->estimated_cost, 2) }}
                                        </p>
                                    </div>
                                @endif

                                @if ($project->billable_resource)
                                    <div>
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Billable Resource</p>
                                        <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ number_format($project->billable_resource, 2) }}
                                        </p>
                                    </div>
                                @endif

                                @if ($project->non_billable_resource)
                                    <div>
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Non-Billable Resource</p>
                                        <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ number_format($project->non_billable_resource, 2) }}
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Team Members -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                        <h2 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Team Members</h2>

                        <div class="space-y-3">
                            @foreach ($project->users as $user)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <img
                                            src="{{ $user->avatar_url }}"
                                            alt="{{ $user->full_name }}"
                                            class="w-8 h-8 rounded-full object-cover border-2 border-[#e3e3e0] dark:border-[#3E3E3A]"
                                        >
                                        <div>
                                            <p class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </p>
                                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                                {{ ucfirst($user->pivot->role) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
