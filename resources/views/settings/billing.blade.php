@extends('layouts.dashboard')

@section('content')
    <div class="p-6 lg:p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Settings
                </a>
            </div>

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold">Billing Information</h1>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-2">
                    View and manage your billing details and subscription
                </p>
            </div>

            <!-- Current Plan -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Current Plan</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Free Plan</p>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">Perfect for getting started</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">$0<span class="text-lg font-normal text-[#706f6c] dark:text-[#A1A09A]">/month</span></p>
                    </div>
                </div>
            </div>

            <!-- Billing Details -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6 mb-6">
                <h2 class="text-lg font-semibold mb-4">Billing Details</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-[#706f6c] dark:text-[#A1A09A]">Company Name</span>
                        <span class="text-[#1b1b18] dark:text-[#EDEDEC] font-medium">{{ $company->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#706f6c] dark:text-[#A1A09A]">Next Billing Date</span>
                        <span class="text-[#1b1b18] dark:text-[#EDEDEC] font-medium">—</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-[#706f6c] dark:text-[#A1A09A]">Payment Method</span>
                        <span class="text-[#1b1b18] dark:text-[#EDEDEC] font-medium">—</span>
                    </div>
                </div>
            </div>

            <!-- Billing History -->
            <div class="bg-white dark:bg-[#161615] rounded-lg shadow-sm border border-[#e3e3e0] dark:border-[#3E3E3A] p-6">
                <h2 class="text-lg font-semibold mb-4">Billing History</h2>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-[#D6D6D6] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">No billing history available</p>
                </div>
            </div>
        </div>
    </div>
@endsection
