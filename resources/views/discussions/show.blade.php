@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-5xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('discussions.index') }}" class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Discussions
                </a>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Discussion Header -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            @if ($discussion->is_private)
                                <svg class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            @endif
                            <h1 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $discussion->title }}</h1>
                            @if ($discussion->type)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400">
                                    {{ $discussion->type }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            <div class="flex items-center gap-2">
                                <img src="{{ $discussion->creator->avatar_url }}" alt="{{ $discussion->creator->full_name }}" class="w-6 h-6 rounded-full">
                                <span>Started by <strong>{{ $discussion->creator->full_name }}</strong></span>
                            </div>

                            @if ($discussion->project)
                                <span>in <strong>{{ $discussion->project->name }}</strong></span>
                            @else
                                <span class="text-amber-600 dark:text-amber-400">Standalone</span>
                            @endif

                            <span>{{ $discussion->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if (auth()->id() === $discussion->created_by || in_array(auth()->user()->getCompanyRole(), ['owner', 'admin']))
                        <div class="flex items-center gap-2">
                            <a
                                href="{{ route('discussions.edit', $discussion) }}"
                                class="px-3 py-1.5 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] transition-all"
                            >
                                Edit
                            </a>
                            <form method="POST" action="{{ route('discussions.destroy', $discussion) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('Delete this discussion? This cannot be undone.')"
                                    class="px-3 py-1.5 text-sm text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900 rounded-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
                                >
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Discussion Body -->
                @if ($discussion->body)
                    <div class="prose dark:prose-invert max-w-none mb-6">
                        {!! nl2br(e($discussion->body)) !!}
                    </div>
                @endif

                <!-- Related Tasks -->
                @if ($discussion->tasks->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">Related Tasks</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($discussion->tasks as $task)
                                <a
                                    href="{{ route('tasks.show', [$task->project, $task]) }}"
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900 rounded-sm hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all"
                                >
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-sm text-blue-800 dark:text-blue-400">{{ $task->task_number }} - {{ $task->title }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Participants -->
                @if ($discussion->participants->count() > 0)
                    <div class="pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-3">Participants</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($discussion->participants as $participant)
                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-full">
                                    <img src="{{ $participant->avatar_url }}" alt="{{ $participant->full_name }}" class="w-5 h-5 rounded-full">
                                    <span class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $participant->full_name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Comments Section -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold mb-6">
                    Comments <span class="text-sm font-normal text-[#706f6c] dark:text-[#A1A09A]">({{ $discussion->comments->count() }})</span>
                </h2>

                <!-- Comment Form -->
                <form method="POST" action="{{ route('discussions.comments.store', $discussion) }}" class="mb-8">
                    @csrf
                    <div class="flex gap-3">
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->full_name }}" class="w-10 h-10 rounded-full">
                        <div class="flex-1">
                            <x-quill-editor
                                name="body"
                                :value="old('body', '')"
                                placeholder="Add a comment... Type @ to mention team members"
                                height="120px"
                                :teamMembers="$teamMembers"
                            />
                            <button
                                type="submit"
                                class="mt-2 px-4 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                            >
                                Post Comment
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Comments List -->
                @if ($discussion->comments->count() > 0)
                    <div class="space-y-6">
                        @foreach ($discussion->comments as $comment)
                            <div class="flex gap-3">
                                <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->full_name }}" class="w-10 h-10 rounded-full">
                                <div class="flex-1">
                                    <div class="bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $comment->user->full_name }}</span>
                                                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>

                                            @if (auth()->id() === $comment->user_id || auth()->id() === $discussion->created_by || in_array(auth()->user()->getCompanyRole(), ['owner', 'admin']))
                                                <form method="POST" action="{{ route('discussions.comments.destroy', [$discussion, $comment]) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        onclick="return confirm('Delete this comment?')"
                                                        class="text-xs text-red-600 dark:text-red-400 hover:underline"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="text-sm text-[#1b1b18] dark:text-[#EDEDEC] prose prose-sm dark:prose-invert max-w-none">
                                            {!! $comment->body !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-[#D6D6D6] mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">No comments yet. Be the first to comment!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
