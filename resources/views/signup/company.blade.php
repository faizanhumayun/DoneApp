@extends('layouts.signup')

@section('content')
    <div>
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-2">Account Setup - Step 2 of 3</h2>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Tell us about your company</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-[#fff2f2] dark:bg-[#1D0002] border border-red-200 dark:border-red-900 rounded-sm">
                <ul class="text-sm text-[#F53003] dark:text-[#FF4433] space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('signup.company.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="company_name" class="block text-sm font-medium mb-2">Company Name</label>
                <input
                    type="text"
                    id="company_name"
                    name="company_name"
                    value="{{ old('company_name') }}"
                    required
                    autofocus
                    maxlength="255"
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('company_name') border-red-500 @enderror"
                    placeholder="Acme Inc."
                >
            </div>

            <div>
                <label for="company_size" class="block text-sm font-medium mb-2">Company Size</label>
                <select
                    id="company_size"
                    name="company_size"
                    required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('company_size') border-red-500 @enderror"
                >
                    <option value="">Select company size</option>
                    @foreach ($company_sizes as $value => $label)
                        <option value="{{ $value }}" {{ old('company_size') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="industry_type" class="block text-sm font-medium mb-2">Industry Type</label>
                <select
                    id="industry_type"
                    name="industry_type"
                    required
                    class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500 @error('industry_type') border-red-500 @enderror"
                >
                    <option value="">Select industry</option>
                    @foreach ($industries as $value => $label)
                        <option value="{{ $value }}" {{ old('industry_type') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                type="submit"
                class="w-full px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
            >
                Continue
            </button>
        </form>
    </div>
@endsection
