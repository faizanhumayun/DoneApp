@extends('layouts.dashboard')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Description Textarea Auto-height */
        .description-textarea {
            min-height: 450px;
            resize: vertical;
        }

        /* Select2 Dark Mode */
        .dark .select2-container--default .select2-selection--single,
        .dark .select2-container--default .select2-selection--multiple {
            background-color: #161615;
            border-color: #3E3E3A;
            color: #EDEDEC;
        }

        .dark .select2-container--default .select2-dropdown {
            background-color: #161615;
            border-color: #3E3E3A;
        }

        .dark .select2-container--default .select2-results__option {
            background-color: #161615;
            color: #EDEDEC;
        }

        .dark .select2-container--default .select2-results__option--highlighted {
            background-color: #1C1C1A;
        }

        .dark .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #1C1C1A;
            border-color: #3E3E3A;
            color: #EDEDEC;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            padding: 6px 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
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
                    <a href="{{ route('tasks.show', [$project, $task]) }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">{{ $task->task_number }}</a>
                    <span>/</span>
                    <span class="text-[#1b1b18] dark:text-[#EDEDEC]">Edit</span>
                </div>

                <h1 class="text-3xl font-semibold">Edit Task: {{ $task->task_number }}</h1>
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

            <form method="POST" action="{{ route('tasks.update', [$project, $task]) }}" x-data="taskForm()">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Task Title -->
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                            <label for="title" class="block text-sm font-medium mb-2">
                                Task Title <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                value="{{ old('title', $task->title) }}"
                                required
                                placeholder="Enter task title..."
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                            >
                        </div>

                        <!-- Description -->
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                            <x-quill-editor
                                name="description"
                                label="Description"
                                :value="old('description', $task->description)"
                                placeholder="Write a description..."
                                height="250px"
                                :teamMembers="$teamMembers"
                            />
                        </div>
                    </div>

                    <!-- Sidebar - Task Properties -->
                    <div class="space-y-6">
                        <!-- Task Properties Card -->
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                            <h2 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Task Properties</h2>

                            <div class="space-y-4">
                                <!-- Priority -->
                                <div>
                                    <label for="priority" class="block text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">
                                        Priority <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="priority"
                                        name="priority"
                                        required
                                        class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] text-sm"
                                    >
                                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ old('priority', $task->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                </div>

                                <!-- Workflow Status -->
                                <div>
                                    <label for="workflow_status_id" class="block text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select
                                        id="workflow_status_id"
                                        name="workflow_status_id"
                                        required
                                        class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] text-sm"
                                    >
                                        @foreach ($workflowStatuses as $status)
                                            <option value="{{ $status->id }}" {{ old('workflow_status_id', $task->workflow_status_id) == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }} {{ !$status->is_active ? '(Inactive)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Assignee -->
                                <div>
                                    <label for="assignee_id" class="block text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">
                                        Assignee
                                    </label>
                                    <select
                                        id="assignee_id"
                                        name="assignee_id"
                                        class="w-full assignee-select"
                                    >
                                        <option value="">Unassigned</option>
                                        @foreach ($projectMembers as $member)
                                            <option value="{{ $member->id }}" {{ old('assignee_id', $task->assignee_id) == $member->id ? 'selected' : '' }}>
                                                {{ $member->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Due Date -->
                                <div>
                                    <label for="due_date" class="block text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">
                                        Due Date
                                    </label>
                                    <input
                                        type="date"
                                        id="due_date"
                                        name="due_date"
                                        value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                                        class="w-full px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] text-sm"
                                    >
                                </div>

                                <!-- Tags -->
                                <div>
                                    <label for="tags" class="block text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">
                                        Tags (Max 10)
                                    </label>
                                    <select
                                        id="tags"
                                        name="tags[]"
                                        multiple
                                        class="w-full tags-select"
                                    >
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->name }}" {{ in_array($tag->id, old('tags', $task->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $tag->name }}
                                            </option>
                                        @endforeach
                                        @foreach ($task->tags as $taskTag)
                                            <option value="{{ $taskTag->name }}" selected>{{ $taskTag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Watchers -->
                                <div>
                                    <label for="watchers" class="block text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mb-1">
                                        Watchers
                                    </label>
                                    <select
                                        id="watchers"
                                        name="watchers[]"
                                        multiple
                                        class="w-full watchers-select"
                                    >
                                        @foreach ($projectMembers as $member)
                                            <option value="{{ $member->id }}" {{ in_array($member->id, old('watchers', $task->watchers->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $member->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                            <div class="space-y-3">
                                <button
                                    type="submit"
                                    class="w-full flex items-center justify-center gap-2 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                                >
                                    Save Changes
                                </button>

                                <a
                                    href="{{ route('tasks.show', [$project, $task]) }}"
                                    class="block w-full text-center px-5 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium rounded-sm transition-all"
                                >
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function taskForm() {
            return {
                init() {
                    // Initialize Select2
                    $('.assignee-select').select2({
                        placeholder: 'Select Assignee',
                        allowClear: true,
                        width: '100%'
                    });

                    $('.tags-select').select2({
                        placeholder: 'Add or create tags',
                        allowClear: true,
                        tags: true,
                        maximumSelectionLength: 10,
                        width: '100%'
                    });

                    $('.watchers-select').select2({
                        placeholder: 'Add watchers',
                        allowClear: true,
                        width: '100%'
                    });
                }
            }
        }
    </script>
@endpush
