<!-- Edit User Modal -->
<div
    x-data="{ open: false, user: null }"
    @open-edit-modal.window="open = true; user = $event.detail"
    @keydown.escape.window="open = false"
    x-show="open"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
>
    <!-- Background overlay -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50"
        @click="open = false"
    ></div>

    <!-- Modal content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="relative bg-white dark:bg-[#161615] rounded-lg shadow-xl max-w-2xl w-full"
            @click.stop
        >
            <form :action="`{{ url('/users') }}/${user?.id}`" method="POST" x-show="user">
                @csrf
                @method('PUT')

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-2xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">Edit User</h2>
                    <button
                        type="button"
                        @click="open = false"
                        class="text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-all"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6">
                    <!-- First Name -->
                    <div>
                        <label for="edit_first_name" class="block text-sm font-medium mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="edit_first_name"
                            name="first_name"
                            :value="user?.first_name"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="edit_last_name" class="block text-sm font-medium mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="edit_last_name"
                            name="last_name"
                            :value="user?.last_name"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                    </div>

                    <!-- Email (Read-only) -->
                    <div>
                        <label for="edit_email" class="block text-sm font-medium mb-2">
                            Email Address
                        </label>
                        <input
                            type="email"
                            id="edit_email"
                            :value="user?.email"
                            readonly
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-[#f5f5f5] dark:bg-[#0a0a0a] text-[#706f6c] dark:text-[#A1A09A] cursor-not-allowed"
                        >
                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">Email cannot be changed</p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="edit_role" class="block text-sm font-medium mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="edit_role"
                            name="role"
                            required
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        >
                            <option value="owner" x-bind:selected="user?.role === 'owner'">Owner</option>
                            <option value="admin" x-bind:selected="user?.role === 'admin'">Admin</option>
                            <option value="member" x-bind:selected="user?.role === 'member'">Member</option>
                            <option value="guest" x-bind:selected="user?.role === 'guest'">Guest</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Status
                        </label>
                        <div class="flex items-center gap-3">
                            <label class="inline-flex items-center cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="status_toggle"
                                    x-model="user.status"
                                    value="active"
                                    true-value="active"
                                    false-value="inactive"
                                    class="sr-only peer"
                                >
                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
                                <span class="ms-3 text-sm font-medium" x-text="user?.status === 'active' ? 'Allow this user to log in' : 'User login disabled'"></span>
                            </label>
                        </div>
                        <input type="hidden" name="status" x-bind:value="user?.status">
                    </div>

                    <!-- About Me -->
                    <div>
                        <label for="edit_about_yourself" class="block text-sm font-medium mb-2">
                            About Me <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">(Optional)</span>
                        </label>
                        <textarea
                            id="edit_about_yourself"
                            name="about_yourself"
                            rows="3"
                            x-text="user?.about_yourself"
                            class="w-full px-4 py-3 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:ring-2 focus:ring-[#1b1b18] dark:focus:ring-[#EDEDEC] focus:border-transparent"
                        ></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <button
                        type="button"
                        @click="open = false"
                        class="px-5 py-2 text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] font-medium rounded-sm transition-all"
                    >
                        Cancel
                    </button>
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
</div>
