@extends('layouts.dashboard')

@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        /* Page Container */
        .tasks-page {
            padding: 32px;
            background: #FAF9F7;
            min-height: 100vh;
        }

        .tasks-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 32px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 600;
            color: #1b1b18;
            margin-bottom: 8px;
        }

        /* Filters Bar */
        .filters-bar {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .filters-row {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        /* Search Input */
        .search-input {
            flex: 1;
            min-width: 250px;
            max-width: 400px;
            padding: 10px 16px 10px 40px;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            font-size: 14px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%236D6D6D' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 12px center;
            transition: border-color 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: #2E8AF7;
        }

        .search-input.searching {
            border-color: #2E8AF7;
            background-color: #F9FBFF;
        }

        /* Filter Select */
        .filter-select {
            min-width: 160px;
            padding: 10px 14px;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            font-size: 14px;
            background: white;
            color: #3A3A3A;
        }

        .filter-select:focus {
            outline: 2px solid #7BB3FF;
            border-color: #7BB3FF;
        }

        /* Add Filter Button */
        .add-filter-btn {
            padding: 10px 16px;
            background: white;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            font-size: 14px;
            color: #555;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .add-filter-btn:hover {
            background: #F5F5F5;
        }

        /* View Toggle */
        .view-toggle {
            display: flex;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            overflow: hidden;
            margin-left: auto;
        }

        .view-toggle-btn {
            padding: 10px 14px;
            background: white;
            border: none;
            cursor: pointer;
            color: #6D6D6D;
            transition: all 0.2s;
        }

        .view-toggle-btn.active {
            background: #2E8AF7;
            color: white;
        }

        .view-toggle-btn:not(:last-child) {
            border-right: 1px solid #D6D6D6;
        }

        /* Active Filters */
        .active-filters {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            background: #E8F3FF;
            border: 1px solid #B3D9FF;
            border-radius: 16px;
            font-size: 13px;
            color: #1E6FD7;
        }

        .filter-chip-label {
            font-weight: 500;
        }

        .filter-chip-value {
            color: #2E8AF7;
        }

        .filter-chip-remove {
            background: none;
            border: none;
            color: #1E6FD7;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .filter-chip-remove:hover {
            color: #0D4FA8;
        }

        /* Additional Filters Panel */
        .additional-filters {
            background: white;
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            padding: 16px;
            margin-top: 12px;
            display: none;
        }

        .additional-filters.show {
            display: block;
        }

        .additional-filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .filter-group label {
            display: block;
            font-size: 13px;
            color: #6D6D6D;
            margin-bottom: 6px;
        }

        /* Card View - Single Row Layout */
        .tasks-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .task-card-row {
            background: white;
            border: 1px solid #E2E2E2;
            border-radius: 8px;
            padding: 16px 20px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .task-card-row:hover {
            border-color: #2E8AF7;
            box-shadow: 0 2px 8px rgba(46, 138, 247, 0.1);
        }

        .task-card-left {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
            min-width: 0;
        }

        .task-number {
            font-size: 13px;
            font-weight: 600;
            color: #6D6D6D;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .task-content {
            flex: 1;
            min-width: 0;
        }

        .task-title {
            font-size: 15px;
            font-weight: 600;
            color: #1b1b18;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .task-description {
            font-size: 13px;
            color: #6D6D6D;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .task-card-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .task-assignee {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .assignee-name {
            font-size: 13px;
            color: #3A3A3A;
            white-space: nowrap;
        }

        .task-unassigned {
            font-size: 13px;
            color: #9B9B9B;
            white-space: nowrap;
        }

        .task-due-date {
            font-size: 13px;
            white-space: nowrap;
        }

        .task-project {
            font-size: 13px;
            color: #9B9B9B;
            white-space: nowrap;
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Priority Badge */
        .priority-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .priority-low { background: #E8F5E9; color: #2E7D32; }
        .priority-medium { background: #FFF3E0; color: #EF6C00; }
        .priority-high { background: #FFEBEE; color: #C62828; }
        .priority-critical { background: #F3E5F5; color: #6A1B9A; }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Avatar */
        .avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #E2E2E2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: #6D6D6D;
        }

        /* Table View */
        .tasks-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .tasks-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .tasks-table th {
            background: #FAFAFA;
            padding: 12px 16px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #6D6D6D;
            border-bottom: 1px solid #E2E2E2;
        }

        .tasks-table td {
            padding: 16px;
            border-bottom: 1px solid #F0F0F0;
            font-size: 14px;
            color: #3A3A3A;
        }

        .tasks-table tr:hover {
            background: #F9FBFF;
        }

        .tasks-table tr {
            cursor: pointer;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 24px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
        }

        /* Loading State */
        #tasksContainer.loading {
            position: relative;
            min-height: 200px;
        }

        #tasksContainer.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin: -20px 0 0 -20px;
            border: 3px solid #E2E2E2;
            border-top-color: #2E8AF7;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            color: #D6D6D6;
        }

        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            color: #3A3A3A;
            margin-bottom: 8px;
        }

        .empty-state-text {
            font-size: 14px;
            color: #6D6D6D;
        }

        /* Select2 Styling */
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #D6D6D6;
            border-radius: 6px;
            min-height: 38px;
        }
    </style>
@endpush

@section('content')
    <div class="tasks-page">
        <div class="tasks-container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">Tasks</h1>
            </div>

            <!-- Filters Bar -->
            <div class="filters-bar">
                <form method="GET" action="{{ route('tasks.index') }}" id="filterForm">
                    <div class="filters-row">
                        <!-- Search -->
                        <input
                            type="text"
                            name="search"
                            id="searchInput"
                            value="{{ request('search') }}"
                            placeholder="Search tasks..."
                            class="search-input"
                            oninput="debouncedSearch()"
                        >

                        <!-- Project Filter -->
                        <select name="project" class="filter-select" onchange="performAjaxSearch()">
                            <option value="">All Projects</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Assignee Filter -->
                        <select name="assignee" class="filter-select" onchange="performAjaxSearch()">
                            <option value="">All Assignees</option>
                            <option value="unassigned" {{ request('assignee') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ request('assignee') == $user->id ? 'selected' : '' }}>
                                    {{ $user->full_name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Status Filter -->
                        <select name="status" class="filter-select" onchange="performAjaxSearch()">
                            <option value="">All Statuses</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Priority Filter -->
                        <select name="priority" class="filter-select" onchange="performAjaxSearch()">
                            <option value="">All Priorities</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>

                        <!-- Sort By -->
                        <select name="sort_by" class="filter-select" onchange="performAjaxSearch()">
                            <option value="updated_last" {{ request('sort_by') === 'updated_last' ? 'selected' : '' }}>Updated (newest)</option>
                            <option value="created" {{ request('sort_by') === 'created' ? 'selected' : '' }}>Created (oldest)</option>
                            <option value="created_last" {{ request('sort_by') === 'created_last' ? 'selected' : '' }}>Created (newest)</option>
                            <option value="due_date" {{ request('sort_by') === 'due_date' ? 'selected' : '' }}>Due Date</option>
                            <option value="title" {{ request('sort_by') === 'title' ? 'selected' : '' }}>Title</option>
                            <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                            <option value="assignee" {{ request('sort_by') === 'assignee' ? 'selected' : '' }}>Assignee</option>
                            <option value="priority" {{ request('sort_by') === 'priority' ? 'selected' : '' }}>Priority</option>
                        </select>

                        <!-- Add Filter Button -->
                        <button type="button" class="add-filter-btn" onclick="toggleAdditionalFilters()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            More Filters
                        </button>

                        <!-- View Toggle -->
                        <div class="view-toggle">
                            <button type="button" class="view-toggle-btn {{ request('view') === 'card' ? 'active' : '' }}" onclick="setView('card')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </button>
                            <button type="button" class="view-toggle-btn {{ !request('view') || request('view') === 'table' ? 'active' : '' }}" onclick="setView('table')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="3" y1="12" x2="21" y2="12"></line>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <line x1="3" y1="18" x2="21" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Additional Filters Panel -->
                    <div class="additional-filters" id="additionalFilters">
                        <div class="additional-filters-grid">
                            <!-- Creator Filter -->
                            <div class="filter-group">
                                <label>Creator</label>
                                <select name="creator" class="filter-select" onchange="performAjaxSearch()">
                                    <option value="">Any Creator</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('creator') == $user->id ? 'selected' : '' }}>
                                            {{ $user->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tags Filter -->
                            <div class="filter-group">
                                <label>Tags</label>
                                <select name="tags[]" class="filter-select tags-select" multiple>
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, (array) request('tags', [])) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Watching Filter -->
                            <div class="filter-group">
                                <label>Watching</label>
                                <select name="watching" class="filter-select" onchange="performAjaxSearch()">
                                    <option value="">All Tasks</option>
                                    <option value="true" {{ request('watching') === 'true' ? 'selected' : '' }}>You are watching</option>
                                </select>
                            </div>

                            <!-- Created Within Days -->
                            <div class="filter-group">
                                <label>Created Within</label>
                                <select name="created_within" class="filter-select" onchange="performAjaxSearch()">
                                    <option value="">Any Time</option>
                                    <option value="1" {{ request('created_within') == '1' ? 'selected' : '' }}>Last 24 hours</option>
                                    <option value="7" {{ request('created_within') == '7' ? 'selected' : '' }}>Last 7 days</option>
                                    <option value="30" {{ request('created_within') == '30' ? 'selected' : '' }}>Last 30 days</option>
                                    <option value="90" {{ request('created_within') == '90' ? 'selected' : '' }}>Last 90 days</option>
                                </select>
                            </div>

                            <!-- Updated Within Days -->
                            <div class="filter-group">
                                <label>Updated Within</label>
                                <select name="updated_within" class="filter-select" onchange="performAjaxSearch()">
                                    <option value="">Any Time</option>
                                    <option value="1" {{ request('updated_within') == '1' ? 'selected' : '' }}>Last 24 hours</option>
                                    <option value="7" {{ request('updated_within') == '7' ? 'selected' : '' }}>Last 7 days</option>
                                    <option value="30" {{ request('updated_within') == '30' ? 'selected' : '' }}>Last 30 days</option>
                                    <option value="90" {{ request('updated_within') == '90' ? 'selected' : '' }}>Last 90 days</option>
                                </select>
                            </div>

                            <!-- Due Date Range -->
                            <div class="filter-group">
                                <label>Due Date From</label>
                                <input type="date" name="due_date_from" value="{{ request('due_date_from') }}" class="filter-select" onchange="performAjaxSearch()">
                            </div>

                            <div class="filter-group">
                                <label>Due Date To</label>
                                <input type="date" name="due_date_to" value="{{ request('due_date_to') }}" class="filter-select" onchange="performAjaxSearch()">
                            </div>
                        </div>
                    </div>

                    <!-- Hidden View Input -->
                    <input type="hidden" name="view" value="{{ request('view', 'table') }}">
                </form>
            </div>

            <!-- Active Filters Chips -->
            @if (request()->hasAny(['search', 'project', 'assignee', 'status', 'priority', 'creator', 'tags', 'watching', 'created_within', 'updated_within', 'due_date_from', 'due_date_to']))
                <div class="active-filters">
                    @if (request('search'))
                        <div class="filter-chip">
                            <span class="filter-chip-label">Search:</span>
                            <span class="filter-chip-value">{{ request('search') }}</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('search')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (request('project'))
                        @php $projectName = $projects->find(request('project'))->name ?? 'Unknown' @endphp
                        <div class="filter-chip">
                            <span class="filter-chip-label">Project:</span>
                            <span class="filter-chip-value">{{ $projectName }}</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('project')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (request('assignee'))
                        @php
                            $assigneeName = request('assignee') === 'unassigned'
                                ? 'Unassigned'
                                : ($users->find(request('assignee'))->full_name ?? 'Unknown');
                        @endphp
                        <div class="filter-chip">
                            <span class="filter-chip-label">Assignee:</span>
                            <span class="filter-chip-value">{{ $assigneeName }}</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('assignee')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (request('status'))
                        @php $statusName = $statuses->find(request('status'))->name ?? 'Unknown' @endphp
                        <div class="filter-chip">
                            <span class="filter-chip-label">Status:</span>
                            <span class="filter-chip-value">{{ $statusName }}</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('status')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (request('priority'))
                        <div class="filter-chip">
                            <span class="filter-chip-label">Priority:</span>
                            <span class="filter-chip-value">{{ ucfirst(request('priority')) }}</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('priority')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (request('creator'))
                        @php $creatorName = $users->find(request('creator'))->full_name ?? 'Unknown' @endphp
                        <div class="filter-chip">
                            <span class="filter-chip-label">Creator:</span>
                            <span class="filter-chip-value">{{ $creatorName }}</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('creator')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (request('watching') === 'true')
                        <div class="filter-chip">
                            <span class="filter-chip-label">Watching:</span>
                            <span class="filter-chip-value">You are watching</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('watching')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (request('created_within'))
                        <div class="filter-chip">
                            <span class="filter-chip-label">Created:</span>
                            <span class="filter-chip-value">Last {{ request('created_within') }} days</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('created_within')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (request('updated_within'))
                        <div class="filter-chip">
                            <span class="filter-chip-label">Updated:</span>
                            <span class="filter-chip-value">Last {{ request('updated_within') }} days</span>
                            <button type="button" class="filter-chip-remove" onclick="removeFilter('updated_within')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Tasks Display Container -->
            <div id="tasksContainer">
                @include('tasks.partials.tasks-list', ['tasks' => $tasks])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Debounce timer for search
        let searchTimeout = null;

        // Perform AJAX search - Define first so other functions can call it
        window.performAjaxSearch = function() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            // Debug: log the parameters
            console.log('AJAX Search Parameters:', params.toString());
            console.log('View parameter:', params.get('view'));

            // Show loading state
            const container = document.getElementById('tasksContainer');
            container.classList.add('loading');
            container.style.opacity = '0.6';
            container.style.pointerEvents = 'none';

            const url = '{{ route('tasks.index') }}?' + params.toString();
            console.log('Fetching URL:', url);

            // Make AJAX request
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Debug: log first 200 chars of response
                console.log('Response HTML (first 200 chars):', html.substring(0, 200));
                console.log('Contains tasks-list:', html.includes('tasks-list'));
                console.log('Contains tasks-table:', html.includes('tasks-table'));

                // Update the tasks container
                container.innerHTML = html;
                container.classList.remove('loading');
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';

                // Remove searching state
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.classList.remove('searching');
                }

                // Update URL without page reload
                const newUrl = '{{ route('tasks.index') }}?' + params.toString();
                window.history.pushState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Search error:', error);
                container.classList.remove('loading');
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';

                // Remove searching state
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.classList.remove('searching');
                }
            });
        }

        // Real-time AJAX search with debounce
        window.debouncedSearch = function() {
            const searchInput = document.getElementById('searchInput');

            // Add searching visual feedback
            searchInput.classList.add('searching');

            // Clear existing timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Set new timeout to search after 500ms of no typing
            searchTimeout = setTimeout(function() {
                window.performAjaxSearch();
            }, 500);
        }

        // Toggle additional filters panel
        window.toggleAdditionalFilters = function() {
            const panel = document.getElementById('additionalFilters');
            panel.classList.toggle('show');
        }

        // Set view mode (card or table)
        window.setView = function(view) {
            console.log('setView called with:', view);
            const form = document.getElementById('filterForm');
            const viewInput = form.querySelector('input[name="view"]');
            console.log('Current view value:', viewInput.value);
            viewInput.value = view;
            console.log('New view value:', viewInput.value);

            // Update button active states immediately
            const cardBtn = document.querySelector('.view-toggle-btn:first-child');
            const tableBtn = document.querySelector('.view-toggle-btn:last-child');

            if (view === 'card') {
                cardBtn.classList.add('active');
                tableBtn.classList.remove('active');
            } else {
                cardBtn.classList.remove('active');
                tableBtn.classList.add('active');
            }

            window.performAjaxSearch();
        }

        // Remove a filter
        window.removeFilter = function(filterName) {
            const form = document.getElementById('filterForm');
            const input = form.querySelector(`[name="${filterName}"], [name="${filterName}[]"]`);

            if (input) {
                if (input.tagName === 'SELECT' && input.hasAttribute('multiple')) {
                    $(input).val(null).trigger('change');
                } else {
                    input.value = '';
                }
            }

            window.performAjaxSearch();
        }

        // Initialize Select2 for tags
        $(document).ready(function() {
            $('.tags-select').select2({
                placeholder: 'Select tags',
                allowClear: true,
                width: '100%'
            });

            $('.tags-select').on('change', function() {
                window.performAjaxSearch();
            });
        });
    </script>
@endpush
