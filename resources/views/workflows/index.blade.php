@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-semibold mb-2">Workflows</h1>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">
                        Manage project workflows and statuses.
                    </p>
                </div>
                <a href="{{ route('workflows.create') }}"
                   class="flex items-center gap-2 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add new project workflow
                </a>
            </div>

            <!-- Success Message -->
            @if (session('message'))
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-900 rounded-sm">
                    <p class="text-sm text-green-800 dark:text-green-400">{{ session('message') }}</p>
                </div>
            @endif

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

            <!-- Workflow Cards -->
            @if ($workflows->isEmpty())
                <div class="text-center py-16">
                    <svg class="mx-auto w-16 h-16 text-[#706f6c] dark:text-[#A1A09A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">No workflows yet</h3>
                    <p class="text-[#706f6c] dark:text-[#A1A09A] mb-6">Get started by creating your first project workflow.</p>
                    <a href="{{ route('workflows.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add new project workflow
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-data="{ viewModal: false, selectedWorkflow: null }">
                    @foreach ($workflows as $workflow)
                        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 hover:shadow-md transition-shadow">
                            <!-- Workflow Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-1">
                                        {{ $workflow->name }}
                                    </h3>
                                    @if ($workflow->description)
                                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                            {{ Str::limit($workflow->description, 60) }}
                                        </p>
                                    @endif
                                </div>
                                <!-- Actions Dropdown -->
                                <div x-data="{ open: false }" class="relative">
                                    <button
                                        @click="open = !open"
                                        class="p-1 text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-cloak
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#161615] rounded-sm shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] py-1 z-10">
                                        @if (!$workflow->is_builtin)
                                            <a href="{{ route('workflows.edit', $workflow) }}"
                                               class="block px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                                Edit
                                            </a>
                                        @endif
                                        <button type="button"
                                                @click="selectedWorkflow = @js($workflow); viewModal = true; open = false"
                                                class="w-full text-left px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                            View
                                        </button>
                                        <form method="POST" action="{{ route('workflows.duplicate', $workflow) }}" class="block">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                                Duplicate
                                            </button>
                                        </form>
                                        @if ($workflow->canBeDeleted())
                                            <form method="POST" action="{{ route('workflows.destroy', $workflow) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this workflow?')"
                                                  class="block border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a]">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Statuses -->
                            <div class="flex flex-wrap gap-2">
                                @foreach ($workflow->statuses as $status)
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium"
                                          style="background-color: {{ $status->color }}; color: {{ $status->text_color }}">
                                        {{ $status->name }}
                                        @if (!$status->is_active)
                                            <span class="text-xs opacity-75">(Inactive)</span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- View Workflow Modal -->
                    <div x-show="viewModal"
                         x-cloak
                         @keydown.escape.window="viewModal = false"
                         class="fixed inset-0 z-50 overflow-y-auto"
                         style="display: none;">
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
                             @click="viewModal = false"></div>

                        <!-- Modal Content -->
                        <div class="flex items-center justify-center min-h-screen p-4">
                            <div class="relative bg-white dark:bg-[#161615] rounded-lg shadow-xl max-w-2xl w-full border border-[#e3e3e0] dark:border-[#3E3E3A]"
                                 @click.away="viewModal = false">
                                <!-- Modal Header -->
                                <div class="flex items-center justify-between p-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                                    <h2 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]" x-text="selectedWorkflow?.name"></h2>
                                    <button @click="viewModal = false"
                                            class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Modal Body -->
                                <div class="p-6 space-y-6">
                                    <!-- Description -->
                                    <div>
                                        <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">Description</h3>
                                        <p class="text-[#1b1b18] dark:text-[#EDEDEC]" x-text="selectedWorkflow?.description || 'No description provided'"></p>
                                    </div>

                                    <!-- Statuses -->
                                    <div>
                                        <h3 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-3">Statuses</h3>
                                        <div class="space-y-3">
                                            <template x-if="selectedWorkflow?.statuses">
                                                <template x-for="(status, index) in selectedWorkflow.statuses" :key="status.id">
                                                <div class="flex items-center gap-3 p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                                    <!-- Position -->
                                                    <div class="flex items-center justify-center w-8 h-8 rounded-sm bg-white dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A] text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                                                        <span x-text="index + 1"></span>
                                                    </div>

                                                    <!-- Status Name -->
                                                    <div class="flex-1">
                                                        <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]" x-text="status.name"></p>
                                                    </div>
                                                    <!-- Color Badge -->
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-8 h-8 rounded-sm border-2 border-white dark:border-[#3E3E3A] shadow-sm"
                                                             :style="'background-color: ' + status.color"></div>
                                                    </div>

                                                    <!-- Status Badge -->
                                                    <div>
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                                              :class="status.is_active ? 'bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-400'"
                                                              x-text="status.is_active ? 'Active' : 'Inactive'"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Metadata -->
                                    <div class="pt-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                                        <div class="grid grid-cols-3 gap-4 text-xs">
                                            <div>
                                                <p class="text-[#706f6c] dark:text-[#A1A09A]">Created By</p>
                                                <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-1">
                                                    {{$workflow->creator->first_name . ' ' . $workflow->creator->last_name}}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-[#706f6c] dark:text-[#A1A09A]">Created</p>
                                                <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-1" x-text="selectedWorkflow?.created_at ? new Date(selectedWorkflow.created_at).toLocaleDateString() : 'N/A'"></p>
                                            </div>
                                            <div>
                                                <p class="text-[#706f6c] dark:text-[#A1A09A]">Last Updated</p>
                                                <p class="text-[#1b1b18] dark:text-[#EDEDEC] mt-1" x-text="selectedWorkflow?.updated_at ? new Date(selectedWorkflow.updated_at).toLocaleDateString() : 'N/A'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Footer -->
                                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                                    <button @click="viewModal = false"
                                            class="px-4 py-2 text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all">
                                        Close
                                    </button>
                                    <a :href="selectedWorkflow ? '/workflows/' + selectedWorkflow.id + '/edit' : '#'"
                                       class="px-4 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                                        Edit Workflow
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
