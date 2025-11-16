@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold mb-2">My Profile</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">
                    Update your personal information and account settings.
                </p>
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

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6" x-data="{
                showPassword: false,
                showPasswordConfirmation: false,
                showCurrentPassword: false,
                imagePreview: '{{ $user->avatar_url }}',
                removeImage: false,
                updateImagePreview(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                            this.removeImage = false;
                        };
                        reader.readAsDataURL(file);
                    }
                },
                removeProfileImage() {
                    this.removeImage = true;
                    this.imagePreview = 'https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&size=200&background=3B82F6&color=ffffff&bold=true';
                    document.getElementById('profile_image').value = '';
                }
            }">
                @csrf
                @method('PUT')

                <!-- Profile Image Card -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-xl font-semibold mb-6">Profile Picture</h2>

                    <div class="flex items-start gap-6">
                        <!-- Profile Image Preview -->
                        <div class="flex-shrink-0">
                            <img
                                :src="imagePreview"
                                alt="Profile Picture"
                                class="w-32 h-32 rounded-full object-cover border-4 border-[#e3e3e0] dark:border-[#3E3E3A]"
                            >
                        </div>

                        <!-- Upload Controls -->
                        <div class="flex-1 space-y-4">
                            <div>
                                <label for="profile_image" class="block text-sm font-medium mb-2">
                                    Upload New Picture
                                </label>
                                <input
                                    type="file"
                                    id="profile_image"
                                    name="profile_image"
                                    accept="image/jpeg,image/jpg,image/png,image/gif"
                                    @change="updateImagePreview($event)"
                                    class="block w-full text-sm text-[#706f6c] dark:text-[#A1A09A]
                                           file:mr-4 file:py-2 file:px-4
                                           file:rounded-sm file:border-0
                                           file:text-sm file:font-medium
                                           file:bg-[#f5f5f5] dark:file:bg-[#0a0a0a]
                                           file:text-[#1b1b18] dark:file:text-[#EDEDEC]
                                           hover:file:bg-[#e3e3e0] dark:hover:file:bg-[#1C1C1A]
                                           file:cursor-pointer cursor-pointer"
                                >
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                                    JPG, PNG or GIF. Max size 2MB.
                                </p>
                            </div>

                            @if($user->profile_image)
                                <div>
                                    <button
                                        type="button"
                                        @click="removeProfileImage()"
                                        class="text-sm text-red-600 dark:text-red-400 hover:underline"
                                    >
                                        Remove Profile Picture
                                    </button>
                                    <input type="hidden" name="remove_profile_image" :value="removeImage ? '1' : '0'">
                                </div>
                            @endif

                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                If no image is uploaded, an auto-generated avatar with your initials will be displayed.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Personal Information Card -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-xl font-semibold mb-6">Personal Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium mb-2">First Name</label>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name', $user->first_name) }}"
                                required
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('first_name') border-red-500 @enderror"
                            >
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium mb-2">Last Name</label>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name', $user->last_name) }}"
                                required
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('last_name') border-red-500 @enderror"
                            >
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $user->email) }}"
                                required
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror"
                            >
                        </div>

                        <!-- Timezone -->
                        <div>
                            <label for="timezone" class="block text-sm font-medium mb-2">Timezone</label>
                            <select
                                id="timezone"
                                name="timezone"
                                required
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('timezone') border-red-500 @enderror"
                            >
                                @foreach ($timezones as $value => $label)
                                    <option value="{{ $value }}" {{ old('timezone', $user->timezone) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- About Yourself -->
                        <div class="md:col-span-2">
                            <label for="about_yourself" class="block text-sm font-medium mb-2">About Yourself (Optional)</label>
                            <textarea
                                id="about_yourself"
                                name="about_yourself"
                                rows="4"
                                maxlength="500"
                                class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('about_yourself') border-red-500 @enderror"
                                placeholder="Tell us a bit about yourself..."
                            >{{ old('about_yourself', $user->about_yourself) }}</textarea>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">Maximum 500 characters</p>
                        </div>
                    </div>
                </div>

                <!-- Change Password Card -->
                <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm p-6 border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-xl font-semibold mb-2">Change Password</h2>
                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-6">Leave blank if you don't want to change your password.</p>

                    <div class="space-y-4">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium mb-2">Current Password</label>
                            <div class="relative">
                                <input
                                    :type="showCurrentPassword ? 'text' : 'password'"
                                    id="current_password"
                                    name="current_password"
                                    class="w-full px-4 py-2 pr-10 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-500 @enderror"
                                >
                                <button
                                    type="button"
                                    @click="showCurrentPassword = !showCurrentPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#706f6c] dark:text-[#A1A09A]"
                                >
                                    <svg x-show="!showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium mb-2">New Password</label>
                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    name="password"
                                    class="w-full px-4 py-2 pr-10 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror"
                                >
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#706f6c] dark:text-[#A1A09A]"
                                >
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">
                                At least 8 characters with uppercase, lowercase, number, and symbol
                            </p>
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirm New Password</label>
                            <div class="relative">
                                <input
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    class="w-full px-4 py-2 pr-10 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                <button
                                    type="button"
                                    @click="showPasswordConfirmation = !showPasswordConfirmation"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#706f6c] dark:text-[#A1A09A]"
                                >
                                    <svg x-show="!showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPasswordConfirmation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <a
                        href="{{ route('dashboard') }}"
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
@endsection
