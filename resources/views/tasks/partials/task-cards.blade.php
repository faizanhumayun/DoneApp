<!-- Card View - Single Row Layout -->
<div class="tasks-list">
    @foreach ($tasks as $task)
        <div class="task-card-row" onclick="window.location='{{ route('tasks.show', [$task->project, $task]) }}'">
            <div class="task-card-left">
                <span class="task-number">{{ $task->task_number }}</span>
                <div class="task-content">
                    <div class="task-title">{{ $task->title }}</div>
                    @if ($task->description)
                        <div class="task-description">{{ $task->description }}</div>
                    @endif
                </div>
            </div>

            <div class="task-card-right">
                <span class="status-badge" style="background-color: {{ $task->workflowStatus->color }}20; color: {{ $task->workflowStatus->color }};">
                    {{ $task->workflowStatus->name }}
                </span>

                <span class="priority-badge priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>

                @if ($task->assignee)
                    <div class="task-assignee">
                        @if ($task->assignee->profile_image)
                            <img src="{{ asset('storage/' . $task->assignee->profile_image) }}" alt="{{ $task->assignee->full_name }}" class="avatar" title="{{ $task->assignee->full_name }}">
                        @else
                            <div class="avatar-placeholder" title="{{ $task->assignee->full_name }}">
                                {{ substr($task->assignee->first_name, 0, 1) }}{{ substr($task->assignee->last_name, 0, 1) }}
                            </div>
                        @endif
                        <span class="assignee-name">{{ $task->assignee->full_name }}</span>
                    </div>
                @else
                    <span class="task-unassigned">Unassigned</span>
                @endif

                @if ($task->due_date)
                    <span class="task-due-date" style="color: {{ $task->due_date->isPast() ? '#C62828' : '#6D6D6D' }};">
                        Due {{ $task->due_date->format('M d') }}
                    </span>
                @endif

                <span class="task-project">{{ $task->project->name }}</span>
            </div>
        </div>
    @endforeach
</div>
