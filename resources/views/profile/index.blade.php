@extends('layouts.app')

@section('title', 'Profile & Password')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Title -->
    <div class="border-b border-slate-200 pb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Account Profile &amp; Security
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Manage your official staff details and update your private login password.
            </p>
        </div>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <!-- Default Password Notice (if applicable) -->
    @if($user->mustChangePassword())
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start space-x-3.5 shadow-xs animate-fade-in">
            <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-amber-950">Action Recommended: Update Default Password</h4>
                <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                    Your account is using a temporary password issued by the Chaplaincy Office. You must set your own private password below before you can use the rest of the portal.
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- PRIMARY: Change Password Card (order-1 on mobile so it is immediately visible without scrolling) -->
        <div class="lg:col-span-2 order-1 lg:order-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5 sm:p-7 space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">
                            Change Account Password
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Set a secure private password known only to you.
                        </p>
                    </div>
                    <span class="w-8 h-8 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                </div>

                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs space-y-1">
                        @foreach ($errors->all() as $err)
                            <div class="flex items-center space-x-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#DC2626] shrink-0"></span>
                                <span>{{ $err }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf

                    <!-- Current / Default Password -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Current / Default Password</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" required placeholder="Enter your current or temporary password" class="w-full pl-3.5 pr-10 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-white transition">
                            <button type="button" onclick="togglePassVisibility('current_password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer" title="Toggle password visibility">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- New Password & Confirmation -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">New Password</label>
                            <div class="relative">
                                <input type="password" id="new_password" name="password" required placeholder="Minimum 8 characters" class="w-full pl-3.5 pr-10 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-white transition">
                                <button type="button" onclick="togglePassVisibility('new_password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer" title="Toggle password visibility">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Confirm New Password</label>
                            <div class="relative">
                                <input type="password" id="new_password_confirmation" name="password_confirmation" required placeholder="Re-type new password" class="w-full pl-3.5 pr-10 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-white transition">
                                <button type="button" onclick="togglePassVisibility('new_password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer" title="Toggle password visibility">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 text-[11px] space-y-1">
                        <div class="font-semibold text-slate-800">Password Requirements:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                            <li>Must be at least 8 characters in length</li>
                            <li>Must differ from your initial default password</li>
                            <li>Will be required for all future sign-ins to your account</li>
                        </ul>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-6 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold shadow-xs transition active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Save New Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECONDARY: Official Profile Summary & Phone (order-2 on mobile, sidebar on desktop) -->
        <div class="lg:col-span-1 order-2 lg:order-1 space-y-5">
            <!-- Profile Identity Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5 space-y-4">
                <div class="text-center space-y-2">
                    <div class="w-16 h-16 rounded-full bg-[#DC2626] text-white flex items-center justify-center font-bold text-xl mx-auto shadow-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">{{ $user->name }}</h2>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-[#DC2626] border border-red-100">
                            {{ $user->getRoleDisplayName() }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-3 space-y-2.5 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Official Email</span>
                        <span class="text-slate-800 font-medium break-all">{{ $user->email }}</span>
                    </div>

                    @if($user->club)
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Assigned Fellowship</span>
                            <span class="text-slate-800 font-medium">{{ $user->club?->name }} ({{ $user->club?->code }})</span>
                        </div>
                    @endif

                    @if($user->position)
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Official Position</span>
                            <span class="text-slate-800 font-medium">{{ $user->position }}</span>
                        </div>
                    @endif

                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Account Status</span>
                        <span class="inline-flex items-center space-x-1.5 text-emerald-700 font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="capitalize">{{ $user->status ?? 'Active' }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contact Phone Form -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center justify-between">
                    <span>Contact Phone</span>
                    <span class="text-[10px] text-slate-400 font-normal">Optional</span>
                </h3>
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-medium text-slate-600 mb-1">Mobile / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+263 77 123 4567" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-900 bg-white">
                    </div>
                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 active:scale-[0.99] text-slate-800 text-xs font-semibold transition cursor-pointer">
                        Update Contact
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<script>
    function togglePassVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            btn.classList.add('text-[#DC2626]');
        } else {
            input.type = 'password';
            btn.classList.remove('text-[#DC2626]');
        }
    }
</script>
@endsection
