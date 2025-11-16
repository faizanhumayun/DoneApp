@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Start Discussion</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    Create a new discussion to collaborate with your team
                </p>
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

            <!-- Create Discussion Form -->
            <form method="POST" action="{{ route('discussions.store') }}">
                @csrf

                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 space-y-6">
                    <!-- Project (Optional) -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium mb-2">
                            Project <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">(Optional)</span>
                        </label>
                        <select
                            id="project_id"
                            name="project_id"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                            <option value="">No project (standalone discussion)</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', request('project_id')) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                            You can create discussions that aren't tied to a project
                        </p>
                    </div>

                    <!-- Discussion Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium mb-2">
                            Discussion Title <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="E.g. API changes for Q3 release"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                    </div>

                    <!-- Discussion Details -->
                    <div>
                        <label for="body" class="block text-sm font-medium mb-2">
                            Discussion Details <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">(Optional)</span>
                        </label>
                        <textarea
                            id="body"
                            name="body"
                            rows="6"
                            placeholder="Add context, questions, or information about this discussion..."
                            class="w-full px-4 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >{{ old('body') }}</textarea>
                    </div>

                    <!-- Discussion Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium mb-2">
                            Discussion Type <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">(Optional)</span>
                        </label>
                        <select
                            id="type"
                            name="type"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                            <option value="">Select a type...</option>
                            <option value="General" {{ old('type') === 'General' ? 'selected' : '' }}>General</option>
                            <option value="Design" {{ old('type') === 'Design' ? 'selected' : '' }}>Design</option>
                            <option value="Engineering" {{ old('type') === 'Engineering' ? 'selected' : '' }}>Engineering</option>
                            <option value="Support" {{ old('type') === 'Support' ? 'selected' : '' }}>Support</option>
                            <option value="Announcement" {{ old('type') === 'Announcement' ? 'selected' : '' }}>Announcement</option>
                        </select>
                    </div>

                    <!-- Related Tasks -->
                    @if ($fromTask)
                        <div>
                            <label class="block text-sm font-medium mb-2">Related Tasks</label>
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900 rounded-sm">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <span class="text-sm font-medium text-blue-800 dark:text-blue-400">{{ $fromTask->task_number }} - {{ $fromTask->title }}</span>
                                </div>
                            </div>
                            <input type="hidden" name="related_tasks[]" value="{{ $fromTask->id }}">
                        </div>
                    @endif

                    <!-- Privacy -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Privacy</label>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm cursor-pointer hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] transition-all">
                                <input
                                    type="radio"
                                    name="is_private"
                                    value="0"
                                    {{ old('is_private', '0') === '0' ? 'checked' : '' }}
                                    class="mt-0.5"
                                >
                                <div>
                                    <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Public</div>
                                    <div class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Visible to all project members (or all workspace members if standalone)</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm cursor-pointer hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] transition-all">
                                <input
                                    type="radio"
                                    name="is_private"
                                    value="1"
                                    {{ old('is_private') === '1' ? 'checked' : '' }}
                                    class="mt-0.5"
                                >
                                <div>
                                    <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Private</div>
                                    <div class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Only invited members can see this discussion</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Invite Members (shown for private discussions) -->
                    <div id="invite-members-section" style="display: none;">
                        <label class="block text-sm font-medium mb-2">
                            Invite Members <span class="text-red-500">*</span>
                        </label>
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900 rounded-sm mb-3">
                            <p class="text-sm text-amber-800 dark:text-amber-400">
                                Private discussions require at least one invited member. You can invite both team members and guests.
                            </p>
                        </div>
                        <div class="space-y-2 max-h-60 overflow-y-auto border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm p-3">
                            @if ($companyMembers->count() > 0)
                                @foreach ($companyMembers as $member)
                                    @php
                                        $memberRole = $member->companies->first()->pivot->role ?? 'member';
                                        $roleBadgeColors = [
                                            'owner' => 'bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-400',
                                            'admin' => 'bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-400',
                                            'member' => 'bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-400',
                                            'guest' => 'bg-amber-100 dark:bg-amber-900/20 text-amber-800 dark:text-amber-400',
                                        ];
                                    @endphp
                                    <label class="flex items-center gap-3 p-2 hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm cursor-pointer">
                                        <input
                                            type="checkbox"
                                            name="participants[]"
                                            value="{{ $member->id }}"
                                            {{ in_array($member->id, old('participants', [])) ? 'checked' : '' }}
                                        >
                                        <img src="{{ $member->avatar_url }}" alt="{{ $member->full_name }}" class="w-8 h-8 rounded-full">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">{{ $member->full_name }}</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $roleBadgeColors[$memberRole] ?? $roleBadgeColors['member'] }}">
                                                    {{ ucfirst($memberRole) }}
                                                </span>
                                            </div>
                                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $member->email }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] p-2">No members available</p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <button
                            type="submit"
                            class="flex-1 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                        >
                            Create Discussion
                        </button>
                        <a
                            href="{{ route('discussions.index') }}"
                            class="px-5 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium rounded-sm transition-all"
                        >
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inviteSection = document.getElementById('invite-members-section');
            const privateRadios = document.querySelectorAll('input[name="is_private"]');

            console.log('Privacy toggle script loaded');
            console.log('Found radios:', privateRadios.length);
            console.log('Found invite section:', inviteSection !== null);

            function toggleInviteSection() {
                const selectedRadio = document.querySelector('input[name="is_private"]:checked');
                console.log('Selected radio value:', selectedRadio ? selectedRadio.value : 'none');

                if (selectedRadio && selectedRadio.value === '1') {
                    console.log('Showing invite section');
                    inviteSection.style.display = 'block';
                } else {
                    console.log('Hiding invite section');
                    inviteSection.style.display = 'none';
                }
            }

            // Add change listeners
            privateRadios.forEach(radio => {
                radio.addEventListener('change', toggleInviteSection);
            });

            // Initialize on page load
            toggleInviteSection();
        });
    </script>
    @endpush
@endsection
