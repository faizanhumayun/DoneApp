@extends('layouts.signup')

@section('content')
    <div x-data="teamInvitations()">
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-2">Account Setup - Step 3 of 3</h2>
            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Invite your team members (optional)</p>
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

        <form method="POST" action="{{ route('signup.team.submit') }}" class="space-y-4">
            @csrf

            <div class="space-y-3">
                <template x-for="(email, index) in emails" :key="index">
                    <div class="flex gap-2">
                        <input
                            type="email"
                            :name="'team_member_emails[' + index + ']'"
                            x-model="emails[index]"
                            maxlength="255"
                            class="flex-1 px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="teammate@company.com"
                        >
                        <button
                            type="button"
                            @click="removeEmail(index)"
                            x-show="emails.length > 1"
                            class="px-3 py-2 border border-[#19140035] dark:border-[#3E3E3A] hover:border-red-500 dark:hover:border-red-500 text-[#706f6c] dark:text-[#A1A09A] hover:text-red-500 rounded-sm transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>

                <button
                    type="button"
                    @click="addEmail()"
                    x-show="emails.length < {{ config('signup.max_invitations_per_signup', 10) }}"
                    class="w-full px-5 py-2 border border-dashed border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm transition-all flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add another email
                </button>

                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                    You can invite up to {{ config('signup.max_invitations_per_signup', 10) }} team members
                </p>
            </div>

            <div class="flex gap-3 pt-4">
                <button
                    type="submit"
                    formaction="{{ route('signup.team.skip') }}"
                    class="flex-1 px-5 py-2 border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm transition-all"
                >
                    Skip for now
                </button>
                <button
                    type="submit"
                    class="flex-1 px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] hover:bg-black dark:hover:bg-white font-medium rounded-sm transition-all"
                >
                    Finish Setup
                </button>
            </div>
        </form>
    </div>

    <script>
        function teamInvitations() {
            return {
                emails: [''],

                addEmail() {
                    if (this.emails.length < {{ config('signup.max_invitations_per_signup', 10) }}) {
                        this.emails.push('');
                    }
                },

                removeEmail(index) {
                    if (this.emails.length > 1) {
                        this.emails.splice(index, 1);
                    }
                }
            }
        }
    </script>
@endsection
