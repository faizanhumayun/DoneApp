<!-- Table View -->
<div class="tasks-table">
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Assignee</th>
                <th>Project</th>
                <th>Due Date</th>
                <th>Updated</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr onclick="window.location='{{ route('tasks.show', [$task->project, $task]) }}'">
                    <td>
                        <div style="font-weight: 600;">{{ $task->task_number }}</div>
                        <div style="color: #6D6D6D; font-size: 13px; margin-top: 2px;">{{ $task->title }}</div>
                    </td>
                    <td>
                        <span class="status-badge" style="background-color: {{ $task->workflowStatus->color }}20; color: {{ $task->workflowStatus->color }};">
                            {{ $task->workflowStatus->name }}
                        </span>
                    </td>
                    <td>
                        <span class="priority-badge priority-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                    </td>
                    <td>
                        @if ($task->assignee)
                            <div style="display: flex; align-items: center; gap: 8px;">
                                @if ($task->assignee->profile_image)
                                    <img src="{{ asset('storage/' . $task->assignee->profile_image) }}" alt="{{ $task->assignee->full_name }}" class="avatar">
                                @else
                                    <div class="avatar-placeholder">
                                        {{ substr($task->assignee->first_name, 0, 1) }}{{ substr($task->assignee->last_name, 0, 1) }}
                                    </div>
                                @endif
                                {{ $task->assignee->full_name }}
                            </div>
                        @else
                            <span style="color: #9B9B9B;">Unassigned</span>
                        @endif
                    </td>
                    <td>{{ $task->project->name }}</td>
                    <td>
                        @if ($task->due_date)
                            <span style="color: {{ $task->due_date->isPast() ? '#C62828' : '#3A3A3A' }};">
                                {{ $task->due_date->format('M d, Y') }}
                            </span>
                        @else
                            <span style="color: #9B9B9B;">—</span>
                        @endif
                    </td>
                    <td style="color: #6D6D6D; font-size: 13px;">
                        {{ $task->updated_at->diffForHumans() }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
