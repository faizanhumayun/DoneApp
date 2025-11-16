<!-- Tasks Display -->
@if ($tasks->isEmpty())
    <div class="empty-state">
        <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
        </svg>
        <div class="empty-state-title">No tasks found</div>
        <div class="empty-state-text">Try adjusting your filters or create a new task</div>
    </div>
@else
    @if (request('view') === 'card')
        @include('tasks.partials.task-cards', ['tasks' => $tasks])
    @else
        @include('tasks.partials.task-table', ['tasks' => $tasks])
    @endif

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $tasks->links() }}
    </div>
@endif
