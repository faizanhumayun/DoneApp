@extends('layouts.dashboard')

@push('styles')
    <style>
        /* Description text styling */
        .task-description {
            white-space: pre-wrap;
            line-height: 1.6;
        }
    </style>
@endpush

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                    <a href="{{ route('projects.index') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">Projects</a>
                    <span>/</span>
                    <a href="{{ route('projects.show', $project) }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">{{ $project->name }}</a>
                    <span>/</span>
                    <span class="text-[#1b1b18] dark:text-[#EDEDEC]">{{ $task->task_number }}</span>
                </div>

                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $task->task_number }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  style="background-color: {{ $task->workflowStatus->color }}; color: {{ $task->workflowStatus->text_color }}">
                                {{ $task->workflowStatus->name }}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  style="background-color: {{ $task->priority_color }}; color: {{ $task->priority_text_color }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </div>
                        <h1 class="text-3xl font-semibold">{{ $task->title }}</h1>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (in_array($userRole, ['owner', 'admin']) || $task->created_by === auth()->id())
                            <form method="POST" action="{{ route('tasks.duplicate', [$project, $task]) }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex items-center gap-2 px-4 py-2 bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#e3e3e0] dark:hover:bg-[#1C1C1A] font-medium rounded-sm transition-all"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Duplicate
                                </button>
                            </form>

                            <a href="{{ route('tasks.edit', [$project, $task]) }}"
                               class="flex items-center gap-2 px-4 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if (session('message'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('message') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Description -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                        <h2 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Description</h2>
                        @if ($task->description)
                            <div class="task-description text-sm text-[#1b1b18] dark:text-[#EDEDEC] prose prose-sm dark:prose-invert max-w-none">{!! $task->description !!}</div>
                        @else
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] italic">No description provided.</p>
                        @endif
                    </div>

                    <!-- Comments Section -->
                    <div id="comments" class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                        <h2 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">
                            Comments ({{ $task->comments->count() }})
                        </h2>

                        <!-- Comment Form -->
                        <form method="POST" action="{{ route('task-comments.store', [$project, $task]) }}" class="mb-6">
                            @csrf
                            <div class="mb-3">
                                <x-quill-editor
                                    name="comment"
                                    :value="old('comment', '')"
                                    placeholder="Write a comment... Type @ to mention team members"
                                    height="120px"
                                    :teamMembers="$teamMembers"
                                />
                            </div>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all text-sm"
                            >
                                Add Comment
                            </button>
                        </form>

                        <!-- Comments List -->
                        <div class="space-y-4">
                            @forelse ($task->comments as $comment)
                                <div class="flex gap-3 pb-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A] last:border-0">
                                    <img
                                        src="{{ $comment->user->avatar_url }}"
                                        alt="{{ $comment->user->full_name }}"
                                        class="w-8 h-8 rounded-full object-cover flex-shrink-0"
                                    >
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                                {{ $comment->user->full_name }}
                                            </span>
                                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]" title="{{ $comment->created_at->format('M d, Y h:i A') }}">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] prose prose-sm dark:prose-invert max-w-none">
                                            {!! $comment->comment !!}
                                        </div>

                                        @if (in_array($userRole, ['owner', 'admin']) || $comment->user_id === auth()->id())
                                            <form method="POST" action="{{ route('task-comments.destroy', [$project, $task, $comment]) }}" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this comment?')"
                                                    class="text-xs text-red-600 dark:text-red-400 hover:underline"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] italic">No comments yet. Be the first to comment!</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Activity Log -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                        <h2 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Activity</h2>

                        <div class="space-y-3">
                            @forelse ($task->activityLogs->sortByDesc('created_at') as $log)
                                <div class="flex gap-3 text-sm">
                                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-[#f5f5f5] dark:bg-[#0a0a0a] flex items-center justify-center">
                                        @if ($log->action === 'created')
                                            <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif (str_contains($log->action, 'status'))
                                            <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                            </svg>
                                        @elseif (str_contains($log->action, 'comment'))
                                            <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[#1b1b18] dark:text-[#EDEDEC]">{{ $log->description }}</p>
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]" title="{{ $log->created_at->format('M d, Y h:i A') }}">
                                            {{ $log->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] italic">No activity yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Task Details -->
                <div class="space-y-6">
                    <!-- Task Information -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                        <h2 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Details</h2>

                        <div class="space-y-3">
                            <!-- Workflow Status -->
                            <div id="status-section" x-data="{ currentStatus: {{ $task->workflow_status_id }} }">
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-2">Status</p>
                                @if(isset($task->workflowStatus->is_final) && $task->workflowStatus->is_final)
                                    <!-- Status is final - show locked message -->
                                    <div class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                        <div class="flex items-center gap-2 mb-2">
                                            <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-medium">Task is closed</span>
                                        </div>
                                        <span id="status-badge" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              style="background-color: {{ $task->workflowStatus->color }}; color: {{ $task->workflowStatus->text_color }}">
                                            {{ $task->workflowStatus->name }}
                                        </span>
                                    </div>
                                @else
                                    <!-- Status can be changed -->
                                    @if($task->workflowStatus->name !== 'Closed')
                                        <div class="relative">
                                            <select
                                                x-model="currentStatus"
                                                @change="updateTaskStatus($event.target.value)"
                                                class="w-full px-3 py-2 text-sm border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                                style="appearance: none;">
                                                @foreach($workflowStatuses as $status)
                                                    @php
                                                        // Disable Open and Closed statuses for assignees (only owner/admin/creator can use them)
                                                        $isRestrictedStatus = in_array($status->name, ['Open', 'Closed']);
                                                        $canUseRestrictedStatus = in_array($userRole, ['owner', 'admin']) || $task->created_by === auth()->id();
                                                        $shouldShow = !$isRestrictedStatus || $canUseRestrictedStatus || $task->workflow_status_id == $status->id;
                                                    @endphp
                                                    @if($shouldShow)
                                                        <option value="{{ $status->id }}" {{ $task->workflow_status_id == $status->id ? 'selected' : '' }}>
                                                            {{ $status->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>

                                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                                <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Current status badge display -->
                                    <div class="mt-2">
                                        <span id="status-badge" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                              style="background-color: {{ $task->workflowStatus->color }}; color: {{ $task->workflowStatus->text_color }}">
                                            {{ $task->workflowStatus->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Assignee -->
                            <div x-data="{ currentAssignee: {{ $task->assignee_id ?? 'null' }} }">
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-2">Assignee</p>
                                <div class="relative">
                                    <select
                                        x-model="currentAssignee"
                                        @change="updateTaskAssignee($event.target.value)"
                                        class="w-full px-3 py-2 text-sm border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                        style="appearance: none;">
                                        <option value="">Unassigned</option>
                                        @foreach($projectMembers as $member)
                                            <option value="{{ $member->id }}" {{ $task->assignee_id == $member->id ? 'selected' : '' }}>
                                                {{ $member->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Current assignee display -->
                                <div class="mt-2" id="assignee-display">
                                    @if ($task->assignee)
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $task->assignee->avatar_url }}" alt="{{ $task->assignee->full_name }}" class="w-6 h-6 rounded-full" id="assignee-avatar">
                                            <span class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]" id="assignee-name">{{ $task->assignee->full_name }}</span>
                                        </div>
                                    @else
                                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]" id="assignee-name">Unassigned</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Due Date -->
                            @if ($task->due_date)
                                <div>
                                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Due Date</p>
                                    <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </p>
                                </div>
                            @endif

                            <!-- Creator -->
                            <div>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Created By</p>
                                <div class="flex items-center gap-2">
                                    <img src="{{ $task->creator->avatar_url }}" alt="{{ $task->creator->full_name }}" class="w-6 h-6 rounded-full">
                                    <span class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $task->creator->full_name }}</span>
                                </div>
                            </div>

                            <!-- Created Date -->
                            <div>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Created</p>
                                <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $task->created_at->format('M d, Y') }}
                                </p>
                            </div>

                            <!-- Last Updated -->
                            <div>
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">Last Updated</p>
                                <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $task->updated_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if ($task->tags->count() > 0)
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                            <h2 class="text-lg font-semibold mb-3 text-[#1b1b18] dark:text-[#EDEDEC]">Tags</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($task->tags as $tag)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-white"
                                          style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Watchers -->
                    @if ($task->watchers->count() > 0)
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                            <h2 class="text-lg font-semibold mb-3 text-[#1b1b18] dark:text-[#EDEDEC]">Watchers</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($task->watchers as $watcher)
                                    <div class="flex items-center gap-2" title="{{ $watcher->full_name }}">
                                        <img src="{{ $watcher->avatar_url }}" alt="{{ $watcher->full_name }}" class="w-8 h-8 rounded-full">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Delete Task -->
                    @if (in_array($userRole, ['owner', 'admin']) || $task->created_by === auth()->id())
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                            <h2 class="text-lg font-semibold mb-3 text-red-600 dark:text-red-400">Danger Zone</h2>
                            <form method="POST" action="{{ route('tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="w-full px-4 py-2 bg-red-600 text-white hover:bg-red-700 font-medium rounded-sm transition-all text-sm"
                                >
                                    Delete Task
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateTaskAssignee(assigneeId) {
            // Show loading state
            const assigneeDisplay = document.getElementById('assignee-display');
            const originalContent = assigneeDisplay.innerHTML;
            assigneeDisplay.innerHTML = '<span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Updating...</span>';

            fetch('{{ route('tasks.updateAssignee', [$project, $task]) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    assignee_id: assigneeId || null
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update assignee display
                    if (data.assignee) {
                        assigneeDisplay.innerHTML = `
                            <div class="flex items-center gap-2">
                                <img src="${data.assignee.avatar_url}" alt="${data.assignee.full_name}" class="w-6 h-6 rounded-full" id="assignee-avatar">
                                <span class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]" id="assignee-name">${data.assignee.full_name}</span>
                            </div>
                        `;
                    } else {
                        assigneeDisplay.innerHTML = '<p class="text-sm text-[#706f6c] dark:text-[#A1A09A]" id="assignee-name">Unassigned</p>';
                    }

                    // Show success message
                    showSuccessMessage('Assignee updated successfully');
                } else {
                    assigneeDisplay.innerHTML = originalContent;
                    showErrorMessage(data.error || 'Failed to update assignee');
                }
            })
            .catch(error => {
                console.error('Error updating assignee:', error);
                assigneeDisplay.innerHTML = originalContent;
                showErrorMessage('An error occurred while updating assignee');
            });
        }

        function updateTaskStatus(statusId) {
            // Show loading state
            const statusBadge = document.getElementById('status-badge');
            const originalContent = statusBadge.innerHTML;
            statusBadge.innerHTML = '<span class="text-xs">Updating...</span>';

            fetch('{{ route('tasks.updateStatus', [$project, $task]) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    workflow_status_id: statusId
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Status update response:', data);
                if (data.success) {
                    // Update the status badge in the header
                    const headerBadges = document.querySelectorAll('.inline-flex.items-center.px-2.py-1.rounded-full');
                    if (headerBadges.length > 0) {
                        headerBadges[0].style.backgroundColor = data.status.color;
                        headerBadges[0].style.color = data.status.text_color;
                        headerBadges[0].textContent = data.status.name;
                    }

                    // Check if the new status is final (closed) - check both is_final flag and status name
                    const isClosed = data.status.is_final || data.status.name === 'Closed' || data.status.name === 'closed';

                    if (isClosed) {
                        // Replace the entire status section with locked view
                        const statusSection = document.getElementById('status-section');
                        if (statusSection) {
                            statusSection.innerHTML = `
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-2">Status</p>
                                <div class="p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] font-medium">Task is closed</span>
                                    </div>
                                    <span id="status-badge" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                          style="background-color: ${data.status.color}; color: ${data.status.text_color}">
                                        ${data.status.name}
                                    </span>
                                </div>
                            `;
                            console.log('Status section replaced with locked view');
                        } else {
                            console.error('Status section not found');
                        }
                    } else {
                        // Update the sidebar status badge normally
                        statusBadge.style.backgroundColor = data.status.color;
                        statusBadge.style.color = data.status.text_color;
                        statusBadge.textContent = data.status.name;
                        console.log('Status badge updated:', data.status.name);
                    }

                    // Show success message
                    showSuccessMessage('Status updated successfully');
                } else {
                    statusBadge.innerHTML = originalContent;
                    showErrorMessage('Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                statusBadge.innerHTML = originalContent;
                showErrorMessage('An error occurred while updating status');
            });
        }

        function showSuccessMessage(message) {
            const alert = document.createElement('div');
            alert.className = 'fixed top-20 right-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm p-4 shadow-lg z-50';
            alert.innerHTML = `<p class="text-sm text-green-800 dark:text-green-400">${message}</p>`;
            document.body.appendChild(alert);

            setTimeout(() => {
                alert.remove();
            }, 3000);
        }

        function showErrorMessage(message) {
            const alert = document.createElement('div');
            alert.className = 'fixed top-20 right-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900 rounded-sm p-4 shadow-lg z-50';
            alert.innerHTML = `<p class="text-sm text-red-800 dark:text-red-400">${message}</p>`;
            document.body.appendChild(alert);

            setTimeout(() => {
                alert.remove();
            }, 3000);
        }
    </script>
@endpush
