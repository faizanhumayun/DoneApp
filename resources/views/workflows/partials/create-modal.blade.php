<!-- Right-side Slide-over Modal -->
<div x-show="showCreateModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-hidden"
     @keydown.escape.window="closeCreateModal()">
    <!-- Overlay -->
    <div x-show="showCreateModal"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50"
         @click="closeCreateModal()"></div>

    <!-- Slide-over Panel -->
    <div class="fixed inset-y-0 right-0 flex max-w-full">
        <div x-show="showCreateModal"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-2xl">
            <form method="POST" action="{{ route('workflows.store') }}" id="createWorkflowForm"
                  class="flex h-full flex-col bg-white dark:bg-[#161615] shadow-xl">
                @csrf

                <!-- Header -->
                <div class="bg-[#f5f5f5] dark:bg-[#0a0a0a] px-6 py-4 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Create new workflow</h2>
                        <button type="button"
                                @click="closeCreateModal()"
                                class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto px-6 py-6">
                    <!-- Section 1: Create Statuses -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Create Statuses</h3>

                        <!-- Status List -->
                        <div class="space-y-3" x-ref="statusList">
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
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                       x-model="form.statuses[index].is_active"
                                                       :name="'statuses[' + index + '][is_active]'"
                                                       value="1"
                                                       class="sr-only peer">
                                                <div class="w-11 h-6 bg-[#e3e3e0] dark:bg-[#3E3E3A] peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                            </label>
                                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]" x-text="form.statuses[index].is_active ? 'Active' : 'Inactive'"></span>
                                        </div>

                                        <!-- Delete Button - Hidden for first 2 default statuses (Open and Closed) -->
                                        <div class="w-5">
                                            <button type="button"
                                                    x-show="index > 1"
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

                        <p class="mt-2 text-xs text-[#706f6c] dark:text-[#A1A09A]">
                            Inactive statuses cannot be selected in tasks.
                        </p>
                    </div>

                    <!-- Section 2: Workflow Name Settings -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Workflow Settings</h3>

                        <!-- Workflow Name -->
                        <div class="mb-4">
                            <label for="workflow_name" class="block text-sm font-medium mb-2 text-[#1b1b18] dark:text-[#EDEDEC]">
                                Workflow Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="workflow_name"
                                   name="name"
                                   x-model="form.name"
                                   placeholder="e.g. Basic Task Workflow"
                                   required
                                   maxlength="255"
                                   class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="workflow_description" class="block text-sm font-medium mb-2 text-[#1b1b18] dark:text-[#EDEDEC]">
                                Description
                            </label>
                            <textarea id="workflow_description"
                                      name="description"
                                      x-model="form.description"
                                      rows="3"
                                      placeholder="Make it short and sweet..."
                                      maxlength="1000"
                                      class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-[#e3e3e0] dark:border-[#3E3E3A] px-6 py-4 bg-[#f5f5f5] dark:bg-[#0a0a0a]">
                    <div class="flex items-center justify-end gap-3">
                        <button type="button"
                                @click="closeCreateModal()"
                                class="px-5 py-2 text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-white dark:hover:bg-[#161615] rounded-sm transition-all">
                            Cancel
                        </button>
                        <button type="submit"
                                :disabled="!form.name || form.statuses.length === 0"
                                :class="!form.name || form.statuses.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                class="px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all">
                                Create Workflow +
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
