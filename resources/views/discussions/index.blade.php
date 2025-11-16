@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold">Discussions</h1>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                        Start conversations, collaborate with your team
                    </p>
                </div>
                <a
                    href="{{ route('discussions.create') }}"
                    class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                >
                    Start Discussion
                </a>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('success') }}</p>
                </div>
            @endif

            <!-- Search & Filter Bar -->
            <div class="mb-6">
                <form method="GET" action="{{ route('discussions.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search discussions..."
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                    </div>

                    <select
                        name="project"
                        class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                    >
                        <option value="">All Projects</option>
                        <option value="standalone" {{ request('project') === 'standalone' ? 'selected' : '' }}>Standalone</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>

                    <select
                        name="type"
                        class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                    >
                        <option value="all">All Types</option>
                        <option value="General" {{ request('type') === 'General' ? 'selected' : '' }}>General</option>
                        <option value="Design" {{ request('type') === 'Design' ? 'selected' : '' }}>Design</option>
                        <option value="Engineering" {{ request('type') === 'Engineering' ? 'selected' : '' }}>Engineering</option>
                        <option value="Support" {{ request('type') === 'Support' ? 'selected' : '' }}>Support</option>
                        <option value="Announcement" {{ request('type') === 'Announcement' ? 'selected' : '' }}>Announcement</option>
                    </select>

                    <select
                        name="privacy"
                        class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                    >
                        <option value="">Public & Private</option>
                        <option value="public" {{ request('privacy') === 'public' ? 'selected' : '' }}>Public Only</option>
                        <option value="private" {{ request('privacy') === 'private' ? 'selected' : '' }}>Private Only</option>
                    </select>

                    <label class="inline-flex items-center px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] cursor-pointer">
                        <input
                            type="checkbox"
                            name="my_discussions"
                            value="true"
                            {{ request('my_discussions') === 'true' ? 'checked' : '' }}
                            class="mr-2"
                        >
                        <span class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">Only my discussions</span>
                    </label>

                    <button
                        type="submit"
                        class="px-5 py-2 bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#e3e3e0] dark:hover:bg-[#3E3E3A] font-medium rounded-sm transition-all"
                    >
                        Filter
                    </button>

                    @if (request()->hasAny(['search', 'project', 'type', 'privacy', 'my_discussions']))
                        <a
                            href="{{ route('discussions.index') }}"
                            class="px-5 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium rounded-sm transition-all"
                        >
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Discussion List -->
            @if ($discussions->isEmpty())
                <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-[#D6D6D6] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">No discussions found</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($discussions as $discussion)
                        <a href="{{ route('discussions.show', $discussion) }}" class="block">
                            <div class="bg-white dark:bg-[#161615] rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 hover:border-[#2E8AF7] transition-all">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            @if ($discussion->is_private)
                                                <svg class="w-4 h-4 text-[#706f6c] dark:text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                            @endif
                                            <h3 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $discussion->title }}</h3>
                                            @if ($discussion->type)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400">
                                                    {{ $discussion->type }}
                                                </span>
                                            @endif
                                        </div>

                                        @if ($discussion->body)
                                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-3 line-clamp-2">{{ Str::limit(strip_tags($discussion->body), 150) }}</p>
                                        @endif

                                        <div class="flex items-center gap-4 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                            <div class="flex items-center gap-1">
                                                <img src="{{ $discussion->creator->avatar_url }}" alt="{{ $discussion->creator->full_name }}" class="w-5 h-5 rounded-full">
                                                <span>{{ $discussion->creator->full_name }}</span>
                                            </div>

                                            @if ($discussion->project)
                                                <span>{{ $discussion->project->name }}</span>
                                            @else
                                                <span class="text-amber-600 dark:text-amber-400">Standalone</span>
                                            @endif

                                            <span>{{ $discussion->comments_count }} {{ Str::plural('comment', $discussion->comments_count) }}</span>

                                            <span>Last updated {{ $discussion->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    @if ($discussion->participants->count() > 0)
                                        <div class="flex -space-x-2">
                                            @foreach ($discussion->participants->take(3) as $participant)
                                                <img src="{{ $participant->avatar_url }}" alt="{{ $participant->full_name }}" class="w-8 h-8 rounded-full border-2 border-white dark:border-[#161615]" title="{{ $participant->full_name }}">
                                            @endforeach
                                            @if ($discussion->participants->count() > 3)
                                                <div class="w-8 h-8 rounded-full bg-[#f5f5f5] dark:bg-[#0a0a0a] border-2 border-white dark:border-[#161615] flex items-center justify-center text-xs font-medium text-[#706f6c] dark:text-[#A1A09A]">
                                                    +{{ $discussion->participants->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $discussions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
