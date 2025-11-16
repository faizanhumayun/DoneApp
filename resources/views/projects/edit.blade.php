@extends('layouts.dashboard')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #e3e3e0;
            border-radius: 2px;
            padding: 8px 16px;
            background-color: white;
        }
        .dark .select2-container--default .select2-selection--single {
            background-color: #161615;
            border-color: #3E3E3A;
            color: #EDEDEC;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            color: #1b1b18;
        }
        .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #EDEDEC;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-dropdown {
            border: 1px solid #e3e3e0;
            border-radius: 2px;
            background-color: white;
        }
        .dark .select2-dropdown {
            background-color: #161615;
            border-color: #3E3E3A;
        }
        .select2-container--default .select2-results__option {
            padding: 8px 16px;
            color: #1b1b18;
        }
        .dark .select2-container--default .select2-results__option {
            color: #EDEDEC;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3B82F6;
            color: white;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e3e3e0;
            border-radius: 2px;
            padding: 8px 16px;
            background-color: white;
            color: #1b1b18;
        }
        .dark .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: #161615;
            border-color: #3E3E3A;
            color: #EDEDEC;
        }
    </style>
@endpush

@section('content')
    <div class="p-6 lg:p-8" x-data="projectEditor(@js($project))" x-init="init()">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">
                    <a href="{{ route('projects.index') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">Projects</a>
                    <span>/</span>
                    <span class="text-[#1b1b18] dark:text-[#EDEDEC]">Edit</span>
                </div>
                <h1 class="text-3xl font-semibold mb-2">Edit Project</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    Update project details and team members.
                </p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-[#fff2f2] dark:bg-[#1D0002] border border-red-200 dark:border-red-900 rounded-sm">
                    <ul class="text-sm text-[#F53003] dark:text-[#FF4433] space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information Card -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-xl font-semibold mb-6 text-[#1b1b18] dark:text-[#EDEDEC]">Basic Information</h2>

                    <div class="space-y-4">
                        <!-- Project Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium mb-2">
                                Project Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $project->name) }}"
                                required
                                maxlength="100"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                                placeholder="Enter project name"
                            >
                        </div>

                        <!-- Workflow Selection -->
                        <div>
                            <label for="workflow_id" class="block text-sm font-medium mb-2">
                                Workflow <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="workflow_id"
                                name="workflow_id"
                                required
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('workflow_id') border-red-500 @enderror"
                            >
                                <option value="">Select a workflow</option>
                                @foreach ($workflows as $workflow)
                                    <option value="{{ $workflow->id }}" {{ old('workflow_id', $project->workflow_id) == $workflow->id ? 'selected' : '' }}>
                                        {{ $workflow->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                                Choose the workflow that defines the stages for this project
                            </p>
                        </div>

                        <!-- Project Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium mb-2">
                                Project Description (Optional)
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                maxlength="5000"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                placeholder="Describe the project goals and objectives..."
                            >{{ old('description', $project->description) }}</textarea>
                        </div>
                    </div>
                </div>

                @php
                    $userRole = $company->users()->where('user_id', auth()->id())->first()->pivot->role;
                @endphp

                <!-- Financial Information Card (Hidden for Member role) -->
                @if (!in_array($userRole, ['member']))
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <h2 class="text-xl font-semibold mb-6 text-[#1b1b18] dark:text-[#EDEDEC]">Financial Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Estimated Cost -->
                            <div>
                                <label for="estimated_cost" class="block text-sm font-medium mb-2">
                                    Estimated Cost (Optional)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[#706f6c] dark:text-[#A1A09A]">$</span>
                                    <input
                                        type="number"
                                        id="estimated_cost"
                                        name="estimated_cost"
                                        value="{{ old('estimated_cost', $project->estimated_cost) }}"
                                        step="0.01"
                                        min="0"
                                        class="w-full pl-8 pr-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('estimated_cost') border-red-500 @enderror"
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>

                            <!-- Billable Resource -->
                            <div>
                                <label for="billable_resource" class="block text-sm font-medium mb-2">
                                    Billable Resource (Optional)
                                </label>
                                <input
                                    type="number"
                                    id="billable_resource"
                                    name="billable_resource"
                                    value="{{ old('billable_resource', $project->billable_resource) }}"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('billable_resource') border-red-500 @enderror"
                                    placeholder="0"
                                >
                            </div>

                            <!-- Non-Billable Resource -->
                            <div>
                                <label for="non_billable_resource" class="block text-sm font-medium mb-2">
                                    Non-Billable Resource (Optional)
                                </label>
                                <input
                                    type="number"
                                    id="non_billable_resource"
                                    name="non_billable_resource"
                                    value="{{ old('non_billable_resource', $project->non_billable_resource) }}"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('non_billable_resource') border-red-500 @enderror"
                                    placeholder="0"
                                >
                            </div>

                            <!-- Total Estimated Hours -->
                            <div>
                                <label for="total_estimated_hours" class="block text-sm font-medium mb-2">
                                    Total Estimated Hours (Optional)
                                </label>
                                <input
                                    type="number"
                                    id="total_estimated_hours"
                                    name="total_estimated_hours"
                                    value="{{ old('total_estimated_hours', $project->total_estimated_hours) }}"
                                    min="0"
                                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_estimated_hours') border-red-500 @enderror"
                                    placeholder="0"
                                >
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Team Members Card -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-xl font-semibold mb-6 text-[#1b1b18] dark:text-[#EDEDEC]">Team Members</h2>

                    <div class="space-y-3 mb-4">
                        <template x-for="(member, index) in members" :key="index">
                            <div class="flex gap-3 items-start">
                                <div class="flex-1 grid grid-cols-2 gap-3">
                                    <!-- Member Selection -->
                                    <div>
                                        <select
                                            :name="'members[' + index + '][user_id]'"
                                            :class="'member-select-' + index"
                                            x-model="member.user_id"
                                            @change="updateMemberDropdowns()"
                                            required
                                            class="member-select w-full"
                                        >
                                            <option value="">Select Member</option>
                                            @foreach ($companyUsers as $user)
                                                <option value="{{ $user->id }}">
                                                    {{ $user->first_name }} {{ $user->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Role Selection -->
                                    <div>
                                        <select
                                            :name="'members[' + index + '][role]'"
                                            x-model="member.role"
                                            required
                                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="member">Member</option>
                                            <option value="admin">Admin</option>
                                            <option value="owner">Owner</option>
                                            <option value="guest">Guest</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Remove Button -->
                                <button
                                    type="button"
                                    @click="removeMember(index)"
                                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-sm transition-all"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <p x-show="members.length === 0" class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            No team members added yet. Click "Add Member" to invite collaborators.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="addMember()"
                        class="flex items-center gap-2 px-4 py-2 text-sm bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#e3e3e0] dark:hover:bg-[#1C1C1A] rounded-sm transition-all"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Member
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <a
                        href="{{ route('projects.show', $project) }}"
                        class="px-5 py-2 text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function projectEditor(project) {
            return {
                members: Array.isArray(project.users)
                    ? project.users.map(user => ({
                        user_id: user.id,
                        role: user.pivot.role
                    }))
                    : [],
                allUsers: @json($companyUsers->pluck('id')->toArray()),

                init() {
                    this.$nextTick(() => {
                        this.updateMemberDropdowns();
                    });
                },

                addMember() {
                    const newIndex = this.members.length;
                    this.members.push({
                        user_id: '',
                        role: 'member'
                    });

                    // Wait for Alpine to render the new select element
                    this.$nextTick(() => {
                        this.initializeSelect2(newIndex);
                        this.updateMemberDropdowns();
                    });
                },

                removeMember(index) {
                    // Destroy Select2 instance before removing
                    $(`.member-select-${index}`).select2('destroy');
                    this.members.splice(index, 1);

                    this.$nextTick(() => {
                        this.updateMemberDropdowns();
                    });
                },

                initializeSelect2(index) {
                    $(`.member-select-${index}`).select2({
                        placeholder: 'Select Member',
                        allowClear: true,
                        width: '100%'
                    });
                },

                updateMemberDropdowns() {
                    // Get all selected user IDs
                    const selectedUserIds = this.members
                        .map(m => m.user_id)
                        .filter(id => id !== '' && id !== null);

                    // Update each dropdown
                    this.members.forEach((member, index) => {
                        const select = $(`.member-select-${index}`);

                        // Destroy existing Select2 if it exists
                        if (select.data('select2')) {
                            select.select2('destroy');
                        }

                        // Get all options except the currently selected one in this dropdown
                        const otherSelectedIds = selectedUserIds.filter(id => id != member.user_id);

                        // Hide already selected options
                        select.find('option').each(function() {
                            const optionValue = $(this).val();
                            if (optionValue && otherSelectedIds.includes(parseInt(optionValue))) {
                                $(this).prop('disabled', true).hide();
                            } else {
                                $(this).prop('disabled', false).show();
                            }
                        });

                        // Re-initialize Select2
                        select.select2({
                            placeholder: 'Select Member',
                            allowClear: true,
                            width: '100%'
                        });

                        // Update Alpine model when Select2 changes
                        select.on('change', (e) => {
                            this.members[index].user_id = e.target.value;
                            this.$nextTick(() => {
                                this.updateMemberDropdowns();
                            });
                        });
                    });
                }
            }
        }
    </script>
@endsection
