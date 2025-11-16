@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Welcome Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Welcome, {{ auth()->user()->first_name }}!</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Here are your assigned tasks and discussions
                </p>
            </div>

            <!-- My Tasks Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">My Tasks</h2>
                    <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $tasks->total() }} total</span>
                </div>

                @if ($tasks->isEmpty())
                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-[#D6D6D6] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-[#706f6c] dark:text-[#A1A09A]">No tasks assigned yet</p>
                    </div>
                @else
                    <!-- Tasks List -->
                    <div class="space-y-3">
                        @foreach ($tasks as $task)
                            <a href="{{ route('tasks.show', [$task->project, $task]) }}" class="block">
                                <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 hover:border-[#2E8AF7] transition-all">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="text-xs font-semibold text-[#706f6c] dark:text-[#A1A09A]">{{ $task->task_number }}</span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $task->workflowStatus->color }}20; color: {{ $task->workflowStatus->color }};">
                                                    {{ $task->workflowStatus->name }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    @if($task->priority === 'low') bg-green-100 text-green-800
                                                    @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                                    @elseif($task->priority === 'high') bg-red-100 text-red-800
                                                    @else bg-purple-100 text-purple-800
                                                    @endif">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                            </div>
                                            <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ $task->title }}</h3>
                                            @if ($task->description)
                                                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] line-clamp-2">{{ $task->description }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 mt-3 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                                <span>{{ $task->project->name }}</span>
                                                @if ($task->due_date)
                                                    <span class="{{ $task->due_date->isPast() ? 'text-red-600' : '' }}">
                                                        Due {{ $task->due_date->format('M d') }}
                                                    </span>
                                                @endif
                                                <span>Updated {{ $task->updated_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @if ($task->assignee && $task->assignee->id === auth()->id())
                                            <div class="text-xs bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 px-2 py-1 rounded">
                                                Assigned to you
                                            </div>
                                        @else
                                            <div class="text-xs bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400 px-2 py-1 rounded">
                                                Watching
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $tasks->links() }}
                    </div>
                @endif
            </div>

            <!-- My Discussions Section (Placeholder) -->
            @if ($discussions->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold">My Discussions</h2>
                        <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $discussions->count() }} total</span>
                    </div>

                    <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
                        <p class="text-[#706f6c] dark:text-[#A1A09A]">Discussions feature coming soon</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
