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

        /* Project Selector */
        .project-selector {
            width: 240px;
            margin-bottom: 24px;
        }

        .project-selector select {
            width: 100%;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 14px;
            background: white;
            color: #3A3A3A;
        }

        /* Task Title */
        .task-title-input {
            width: 100%;
            font-size: 30px;
            font-weight: 600;
            color: #3A3A3A;
            border: none;
            outline: none;
            padding: 12px 0;
            margin-bottom: 24px;
            background: transparent;
        }

        .task-title-input::placeholder {
            color: #9B9B9B;
        }

        /* Description Editor */
        .description-editor {
            margin-bottom: 24px;
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
        .properties-panel {
            background: #FFFFFF;
            border: 1px dashed #E2E2E2;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .property-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #EEEEEE;
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
            color: #3A3A3A;
            width: 100px;
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
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 14px;
            background: white;
            color: #3A3A3A;
        }

        .property-select:hover {
            border-color: #BBBBBB;
        }

        .property-select:focus {
            outline: 2px solid #7BB3FF;
            border-color: #7BB3FF;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid #EEEEEE;
        }

        .btn-primary {
            background: #2E8AF7;
            color: white;
            font-weight: 500;
            font-size: 14px;
            padding: 0 20px;
            height: 42px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            background: #1E6FD7;
        }

        .btn-secondary {
            background: white;
            color: #555555;
            font-size: 14px;
            font-weight: 500;
            padding: 0 16px;
            height: 42px;
            border: 1px solid #CCCCCC;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: #F5F5F5;
        }

        .btn-cancel {
            background: none;
            border: none;
            color: #8A8A8A;
            font-size: 14px;
            padding: 0 12px;
            cursor: pointer;
            transition: color 0.2s;
        }

        .btn-cancel:hover {
            color: #555555;
            text-decoration: underline;
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
            <a href="{{ route('projects.show', $project) }}" class="back-button">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Back to Project</span>
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

            <form method="POST" action="{{ route('tasks.store', $project) }}" x-data="taskForm()">
                @csrf

                <!-- Project Selector -->
                <div class="project-selector">
                    <select disabled class="property-select">
                        <option>{{ $project->name }}</option>
                    </select>
                </div>

                <!-- Task Title -->
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
                    required
                    placeholder="Enter task title..."
                    class="task-title-input"
                >

                <!-- Description Editor -->
                <div class="description-editor">
                    <textarea
                        id="description"
                        name="description"
                        placeholder="Write a description..."
                        class="description-textarea"
                    >{{ old('description') }}</textarea>
                </div>

                <!-- Task Properties Panel -->
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
                            <select id="assignee_id" name="assignee_id" class="assignee-select property-select">
                                <option value="">Unassigned</option>
                                @foreach ($projectMembers as $member)
                                    <option value="{{ $member->id }}" {{ old('assignee_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
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
                            <select id="workflow_status_id" name="workflow_status_id" required class="property-select">
                                @foreach ($workflowStatuses as $status)
                                    <option value="{{ $status->id }}" {{ old('workflow_status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
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
                            <select id="watchers" name="watchers[]" multiple class="watchers-select">
                                @foreach ($projectMembers as $member)
                                    <option value="{{ $member->id }}" {{ $member->id == auth()->id() ? 'selected' : '' }}>
                                        {{ $member->full_name }}
                                    </option>
                                @endforeach
                            </select>
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
                    <a href="{{ route('projects.show', $project) }}" class="btn-cancel">
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
    </script>
@endpush
