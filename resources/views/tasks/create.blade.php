@extends('layouts.dashboard')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Page Container */
        .task-page-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 28px;
            background-color: #FAF9F7;
            min-height: 100vh;
        }

        .task-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.04);
            padding: 32px;
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #6D6D6D;
            font-size: 14px;
            margin-bottom: 24px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .back-button:hover {
            color: #3A3A3A;
        }

        /* Page Title */
        .page-title-section {
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #F0F0F0;
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: #1b1b18;
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 14px;
            color: #706f6c;
        }

        /* Project Selector */
        .project-selector-wrapper {
            margin-bottom: 32px;
            padding: 20px;
            background: #F9F9F9;
            border-radius: 8px;
            border: 1px solid #E8E8E8;
        }

        .project-selector-label {
            font-size: 13px;
            font-weight: 600;
            color: #3A3A3A;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .project-selector {
            width: 100%;
            max-width: 400px;
        }

        .project-selector select {
            width: 100%;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            padding: 12px 14px;
            background: white;
            color: #3A3A3A;
            cursor: pointer;
            transition: all 0.2s;
        }

        .project-selector select:hover {
            border-color: #BBBBBB;
        }

        .project-selector select:focus {
            outline: 2px solid #7BB3FF;
            border-color: #7BB3FF;
        }

        .project-selector select:disabled {
            background: #F5F5F5;
            cursor: not-allowed;
            color: #9B9B9B;
        }

        /* Task Title */
        .task-title-section {
            margin-bottom: 24px;
        }

        .task-title-label {
            font-size: 13px;
            font-weight: 600;
            color: #3A3A3A;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .task-title-input {
            width: 100%;
            font-size: 24px;
            font-weight: 600;
            color: #1b1b18;
            border: 1px solid #E8E8E8;
            border-radius: 6px;
            outline: none;
            padding: 14px 16px;
            background: white;
            transition: all 0.2s;
        }

        .task-title-input::placeholder {
            color: #9B9B9B;
            font-weight: 500;
        }

        .task-title-input:focus {
            border-color: #7BB3FF;
            outline: 2px solid #7BB3FF;
        }

        /* Description Editor */
        .description-editor {
            margin-bottom: 32px;
        }

        .description-label {
            font-size: 13px;
            font-weight: 600;
            color: #3A3A3A;
            margin-bottom: 8px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .description-textarea {
            width: 100%;
            min-height: 250px;
            border: 1px solid #E4E4E4;
            border-radius: 6px;
            background: white;
            padding: 12px 14px;
            font-size: 15px;
            line-height: 1.6;
            color: #3A3A3A;
            resize: vertical;
        }

        .description-textarea::placeholder {
            color: #9B9B9B;
        }

        .description-textarea:focus {
            outline: 2px solid #7BB3FF;
            border-color: #7BB3FF;
        }

        /* Task Properties Panel */
        .properties-panel-wrapper {
            margin-bottom: 32px;
        }

        .properties-panel-title {
            font-size: 13px;
            font-weight: 600;
            color: #3A3A3A;
            margin-bottom: 12px;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .properties-panel {
            background: #FFFFFF;
            border: 1px solid #E8E8E8;
            border-radius: 8px;
            padding: 0;
            overflow: hidden;
        }

        .property-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            border-bottom: 1px solid #F0F0F0;
            transition: background 0.2s;
        }

        .property-row:hover {
            background: #FAFAFA;
        }

        .property-row:last-child {
            border-bottom: none;
        }

        .property-icon {
            width: 20px;
            height: 20px;
            color: #6D6D6D;
            flex-shrink: 0;
        }

        .property-label {
            font-size: 14px;
            font-weight: 500;
            color: #1b1b18;
            width: 110px;
            flex-shrink: 0;
        }

        .property-control {
            flex: 1;
        }

        /* Priority Chip */
        .priority-chip {
            display: inline-flex;
            align-items: center;
            height: 28px;
            padding: 0 12px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
        }

        .priority-low { background: #E8F5E9; color: #2E7D32; }
        .priority-medium { background: #FFF3E0; color: #EF6C00; }
        .priority-high { background: #FFEBEE; color: #C62828; }
        .priority-critical { background: #F3E5F5; color: #6A1B9A; }

        /* Select Inputs */
        .property-select {
            width: 100%;
            border: 1px solid #E8E8E8;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 14px;
            background: white;
            color: #1b1b18;
            cursor: pointer;
            transition: all 0.2s;
        }

        .property-select:hover {
            border-color: #D6D6D6;
        }

        .property-select:focus {
            outline: 2px solid #7BB3FF;
            border-color: #7BB3FF;
        }

        .property-select:disabled {
            background: #F9F9F9;
            color: #9B9B9B;
            cursor: not-allowed;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
            padding-top: 32px;
            margin-top: 32px;
            border-top: 2px solid #F0F0F0;
        }

        .btn-primary {
            background: #1b1b18;
            color: white;
            font-weight: 600;
            font-size: 15px;
            padding: 0 28px;
            height: 46px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-primary:hover {
            background: #000000;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background: white;
            color: #3A3A3A;
            font-size: 15px;
            font-weight: 500;
            padding: 0 20px;
            height: 46px;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #F9F9F9;
            border-color: #BBBBBB;
        }

        .btn-cancel {
            background: none;
            border: none;
            color: #706f6c;
            font-size: 15px;
            font-weight: 500;
            padding: 0 16px;
            cursor: pointer;
            transition: color 0.2s;
            text-decoration: none;
            height: 46px;
            display: inline-flex;
            align-items: center;
        }

        .btn-cancel:hover {
            color: #1b1b18;
            text-decoration: none;
        }

        /* Tag Chips */
        .tag-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #F0F0F0;
            border-radius: 12px;
            font-size: 13px;
            color: #3A3A3A;
            margin: 2px;
        }

        /* Avatar */
        .avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 36px;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            padding: 6px 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
            color: #3A3A3A;
            font-size: 14px;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            min-height: 36px;
        }
    </style>
@endpush

@section('content')
    <div class="task-page-container">
        <div class="task-card">
            <!-- Back Button -->
            <a href="{{ $project ? route('projects.show', $project) : route('tasks.index') }}" class="back-button">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>{{ $project ? 'Back to Project' : 'Back to Tasks' }}</span>
            </a>

            <!-- Error Messages -->
            @if ($errors->any())
                <div style="margin-bottom: 24px; padding: 16px; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 8px;">
                    <ul style="list-style: disc; padding-left: 20px; margin: 0; color: #991B1B; font-size: 14px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Title -->
            <div class="page-title-section">
                <h1 class="page-title">{{ $project ? 'Create Task' : 'Create New Task' }}</h1>
                <p class="page-subtitle">{{ $project ? 'Add a new task to ' . $project->name : 'Fill in the details below to create a new task' }}</p>
            </div>

            <form method="POST" action="{{ route('tasks.store') }}" x-data="taskForm()">
                @csrf

                <!-- Project Selector -->
                <div class="project-selector-wrapper">
                    <label for="project_id" class="project-selector-label">Project *</label>
                    <div class="project-selector">
                        <select
                            name="project_id"
                            id="project_id"
                            required
                            onchange="handleProjectChange(this.value)"
                        >
                            <option value="">Select a project...</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ ($project && $project->id == $proj->id) || old('project_id') == $proj->id ? 'selected' : '' }}>
                                    {{ $proj->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!$project)
                        <p style="margin-top: 8px; font-size: 13px; color: #706f6c;">
                            Select a project to load workflow statuses and team members
                        </p>
                    @endif
                </div>

                <!-- Task Title -->
                <div class="task-title-section">
                    <label for="title" class="task-title-label">Task Title *</label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="Enter a clear, descriptive title..."
                        class="task-title-input"
                    >
                </div>

                <!-- Description Editor -->
                <div class="description-editor">
                    <label class="description-label">Description</label>
                    <x-quill-editor
                        name="description"
                        :value="old('description', '')"
                        placeholder="Write a detailed description of the task..."
                        height="250px"
                        :teamMembers="$teamMembers"
                    />
                </div>

                <!-- Task Properties Panel -->
                <div class="properties-panel-wrapper">
                    <label class="properties-panel-title">Task Details</label>
                    <div class="properties-panel">
                    <!-- Priority -->
                    <div class="property-row">
                        <svg class="property-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                        </svg>
                        <span class="property-label">Priority</span>
                        <div class="property-control">
                            <select id="priority" name="priority" class="property-select">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                    </div>

                    <!-- Assignee -->
                    <div class="property-row">
                        <svg class="property-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="property-label">Assignee</span>
                        <div class="property-control">
                            <select id="assignee_id" name="assignee_id" class="assignee-select property-select" {{ $projectMembers->isEmpty() ? 'disabled' : '' }}>
                                @if($projectMembers->isEmpty())
                                    <option value="">Select a project first...</option>
                                @else
                                    <option value="">Unassigned</option>
                                    @foreach ($projectMembers as $member)
                                        <option value="{{ $member->id }}" {{ old('assignee_id') == $member->id ? 'selected' : '' }}>
                                            {{ $member->full_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="property-row">
                        <svg class="property-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="property-label">Status</span>
                        <div class="property-control">
                            <select id="workflow_status_id" name="workflow_status_id" required class="property-select" {{ $workflowStatuses->isEmpty() ? 'disabled' : '' }}>
                                @if($workflowStatuses->isEmpty())
                                    <option value="">Select a project first...</option>
                                @else
                                    @foreach ($workflowStatuses as $status)
                                        <option value="{{ $status->id }}" {{ old('workflow_status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Due Date -->
                    <div class="property-row">
                        <svg class="property-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="property-label">Due Date</span>
                        <div class="property-control">
                            <input
                                type="date"
                                id="due_date"
                                name="due_date"
                                value="{{ old('due_date') }}"
                                min="{{ date('Y-m-d') }}"
                                class="property-select"
                            >
                        </div>
                    </div>

                    <!-- Tags -->
                    <div class="property-row">
                        <svg class="property-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <span class="property-label">Tags</span>
                        <div class="property-control">
                            <select id="tags" name="tags[]" multiple class="tags-select">
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Watchers -->
                    <div class="property-row">
                        <svg class="property-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span class="property-label">Watchers</span>
                        <div class="property-control">
                            <select id="watchers" name="watchers[]" multiple class="watchers-select" {{ $projectMembers->isEmpty() ? 'disabled' : '' }}>
                                @if($projectMembers->isNotEmpty())
                                    @foreach ($projectMembers as $member)
                                        <option value="{{ $member->id }}" {{ $member->id == auth()->id() ? 'selected' : '' }}>
                                            {{ $member->full_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button type="submit" name="action" value="create" class="btn-primary">
                        Create task
                    </button>
                    <button type="submit" name="action" value="create_and_add_more" class="btn-secondary">
                        Create & add more
                    </button>
                    <button type="submit" name="action" value="create_and_copy" class="btn-secondary">
                        Create & copy
                    </button>
                    <a href="{{ $project ? route('projects.show', $project) : route('tasks.index') }}" class="btn-cancel">
                        Cancel
                    </a>
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

        // Handle project change - reload page with selected project
        function handleProjectChange(projectId) {
            if (projectId) {
                window.location.href = `/tasks/create/${projectId}`;
            } else {
                window.location.href = '/tasks/create';
            }
        }
    </script>
@endpush
