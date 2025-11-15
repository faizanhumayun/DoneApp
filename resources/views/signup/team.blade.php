@extends('layouts.signup')

@section('content')
    <div>
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
                @for ($i = 0; $i < 5; $i++)
                    <div>
                        <input
                            type="email"
                            name="team_member_emails[{{ $i }}]"
                            value="{{ old('team_member_emails.' . $i) }}"
                            maxlength="255"
                            class="w-full px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm bg-white dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="teammate@company.com (optional)"
                        >
                    </div>
                @endfor

                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                    Enter email addresses for team members you'd like to invite (leave blank to skip)
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
@endsection
