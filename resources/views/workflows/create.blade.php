@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8" x-data="workflowCreator()">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] mb-2">
                    <a href="{{ route('workflows.index') }}" class="hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">Workflows</a>
                    <span>/</span>
                    <span>Create</span>
                </div>
                <h1 class="text-3xl font-semibold">Create New Workflow</h1>
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

            <form method="POST" action="{{ route('workflows.store') }}" class="space-y-8">
                @csrf

                <!-- Section 1: Workflow Settings (Name & Description) -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-xl font-semibold mb-6 text-[#1b1b18] dark:text-[#EDEDEC]">Workflow Settings</h2>

                    <div class="space-y-4">
                        <!-- Workflow Name -->
                        <div>
                            <label for="workflow_name" class="block text-sm font-medium mb-2">
                                Workflow Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="workflow_name"
                                   name="name"
                                   x-model="form.name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Basic Task Workflow"
                                   required
                                   maxlength="255"
                                   class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="workflow_description" class="block text-sm font-medium mb-2">
                                Description
                            </label>
                            <textarea id="workflow_description"
                                      name="description"
                                      x-model="form.description"
                                      rows="3"
                                      placeholder="Make it short and sweet..."
                                      maxlength="1000"
                                      class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Create Statuses -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-xl font-semibold mb-6 text-[#1b1b18] dark:text-[#EDEDEC]">Create Statuses</h2>

                    <!-- Status List -->
                    <div class="space-y-3 mb-4" x-ref="statusContainer">
                        <template x-for="(status, index) in form.statuses" :key="index">
                            <div>
                                <div class="flex items-center gap-3 p-3 bg-[#f5f5f5] dark:bg-[#0a0a0a] rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A]">
                                    <!-- Drag Handle -->
                                    <div class="cursor-move text-[#706f6c] dark:text-[#A1A09A]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                                        </svg>
                                    </div>

                                    <!-- Status Name -->
                                    <input type="text"
                                           x-model="form.statuses[index].name"
                                           :name="'statuses[' + index + '][name]'"
                                           placeholder="Status name"
                                           maxlength="40"
                                           required
                                           :readonly="form.statuses[index].isDefault"
                                           :class="form.statuses[index].isDefault ? 'cursor-not-allowed opacity-60' : ''"
                                           class="flex-1 px-3 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                                    <!-- Color Picker -->
                                    <div x-data="{ showColors: false }" class="relative">
                                        <button type="button"
                                                @click="showColors = !showColors"
                                                class="w-10 h-10 rounded-sm border-2 border-white dark:border-[#3E3E3A] shadow-sm"
                                                :style="'background-color: ' + form.statuses[index].color">
                                        </button>
                                        <input type="hidden"
                                               :name="'statuses[' + index + '][color]'"
                                               x-model="form.statuses[index].color">

                                        <!-- Color Palette -->
                                        <div x-show="showColors"
                                             @click.away="showColors = false"
                                             x-cloak
                                             class="absolute right-0 mt-2 p-3 bg-white dark:bg-[#161615] rounded-sm shadow-lg border border-[#e3e3e0] dark:border-[#3E3E3A] z-20"
                                             style="width: 240px;">
                                            <div class="grid grid-cols-5 gap-2">
                                                <template x-for="color in ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#6366F1', '#8B5CF6', '#EC4899', '#F97316', '#14B8A6', '#06B6D4', '#84CC16', '#A855F7', '#F43F5E', '#64748B', '#6B7280', '#71717A', '#78716C', '#57534E', '#44403C', '#292524']">
                                                    <button type="button"
                                                            @click="form.statuses[index].color = color; showColors = false"
                                                            class="w-8 h-8 rounded-sm border-2 hover:scale-110 transition-transform"
                                                            :class="form.statuses[index].color === color ? 'border-[#1b1b18] dark:border-white' : 'border-transparent'"
                                                            :style="'background-color: ' + color">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Active/Inactive Toggle -->
                                    <div class="flex items-center gap-2">
                                        <!-- Hidden input to ensure unchecked value is sent -->
                                        <input type="hidden" :name="'statuses[' + index + '][is_active]'" value="0">
                                        <label class="relative inline-flex items-center" :class="form.statuses[index].isDefault ? 'cursor-not-allowed opacity-60' : 'cursor-pointer'">
                                            <input type="checkbox"
                                                   x-model="form.statuses[index].is_active"
                                                   :name="'statuses[' + index + '][is_active]'"
                                                   value="1"
                                                   :disabled="form.statuses[index].isDefault"
                                                   class="sr-only peer">
                                            <div class="w-11 h-6 bg-[#e3e3e0] dark:bg-[#3E3E3A] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 peer-disabled:opacity-60"></div>
                                        </label>
                                        <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] w-16" x-text="form.statuses[index].is_active ? 'Active' : 'Inactive'"></span>
                                    </div>

                                    <!-- Delete Button - Hidden for default statuses (Open and Closed) -->
                                    <div class="w-5">
                                        <button type="button"
                                                x-show="!form.statuses[index].isDefault"
                                                @click="removeStatus(index)"
                                                class="text-[#706f6c] dark:text-[#A1A09A] hover:text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Add Status Button - Show after first status (Open) -->
                                <div x-show="index === 0" class="flex justify-center my-3">
                                    <button type="button"
                                            @click="addStatus()"
                                            class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                        Inactive statuses cannot be selected in tasks.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('workflows.index') }}"
                       class="px-5 py-2 text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#f5f5f5] dark:hover:bg-[#0a0a0a] rounded-sm transition-all">
                        Cancel
                    </a>
                    <button type="submit"
                            :disabled="!form.name || form.statuses.length === 0"
                            :class="!form.name || form.statuses.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                        Create Workflow +
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SortableJS for drag and drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        function workflowCreator() {
            return {
                form: {
                    name: '',
                    description: '',
                    statuses: [
                        { name: 'Open', color: '#3B82F6', is_active: true, isDefault: true },
                        { name: 'Closed', color: '#6B7280', is_active: false, isDefault: true }
                    ]
                },

                init() {
                    // Initialize SortableJS after Alpine is ready
                    this.$nextTick(() => {
                        new Sortable(this.$refs.statusContainer, {
                            animation: 150,
                            handle: '.cursor-move',
                            ghostClass: 'opacity-50',
                            onEnd: (evt) => {
                                // Reorder the statuses array
                                const movedItem = this.form.statuses.splice(evt.oldIndex, 1)[0];
                                this.form.statuses.splice(evt.newIndex, 0, movedItem);
                            }
                        });
                    });
                },

                addStatus() {
                    // Insert new status after "Open" (index 1)
                    this.form.statuses.splice(1, 0, {
                        name: '',
                        color: '#3B82F6',
                        is_active: true,
                        isDefault: false
                    });
                },

                removeStatus(index) {
                    if (!this.form.statuses[index].isDefault) {
                        this.form.statuses.splice(index, 1);
                    }
                }
            }
        }
    </script>
@endsection
