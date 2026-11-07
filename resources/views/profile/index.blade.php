@extends('layouts.app')

@section('title', 'Profile & Password')

@section('content')
<div class="page-shell max-w-5xl space-y-6">

    <x-page-header eyebrow="Your account" title="Account Profile & Security"
                   description="Manage your official staff details and update your private login password.">
        <a href="{{ route('dashboard') }}" class="btn btn-ghost">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>
    </x-page-header>

    <!-- Default Password Notice (if applicable) -->
    @if($user->mustChangePassword())
        <div class="flash-warning" role="status">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <h4 class="text-[13px] font-semibold">Action Recommended: Update Default Password</h4>
                <p class="text-[12.5px] mt-0.5 leading-relaxed opacity-90">
                    Your account is using a temporary password issued by the Chaplaincy Office. You must set your own private password below before you can use the rest of the portal.
                </p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- PRIMARY: Change Password Card (order-1 on mobile so it is immediately visible without scrolling) -->
        <div class="lg:col-span-2 order-1 lg:order-2">
            <section class="card" aria-labelledby="password-heading">
                <div class="card-header">
                    <div class="min-w-0">
                        <p class="card-kicker">Security</p>
                        <h2 id="password-heading" class="card-title mt-0.5">Change Account Password</h2>
                        <p class="text-[12.5px] text-slate-500 mt-0.5">Set a secure private password known only to you.</p>
                    </div>
                    <span class="w-9 h-9 rounded-full grid place-items-center shrink-0 text-gold bg-gold-soft ring-1 ring-gold/25" aria-hidden="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                </div>

                <div class="card-body space-y-5">
                    @if ($errors->any())
                        <div class="flash-error !animate-none" role="alert">
                            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <ul class="text-[12.5px] space-y-1">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                        @csrf

                        <!-- Current / Default Password -->
                        <div>
                            <label for="current_password" class="field-label">Current / Default Password</label>
                            <div class="relative">
                                <input type="password" id="current_password" name="current_password" required placeholder="Enter your current or temporary password" class="field pr-10">
                                <button type="button" onclick="togglePassVisibility('current_password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer rounded-r-xl" title="Toggle password visibility" aria-label="Toggle password visibility">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- New Password & Confirmation -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="new_password" class="field-label">New Password</label>
                                <div class="relative">
                                    <input type="password" id="new_password" name="password" required placeholder="Minimum 8 characters" class="field pr-10">
                                    <button type="button" onclick="togglePassVisibility('new_password', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer rounded-r-xl" title="Toggle password visibility" aria-label="Toggle password visibility">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="field-label">Confirm New Password</label>
                                <div class="relative">
                                    <input type="password" id="new_password_confirmation" name="password_confirmation" required placeholder="Re-type new password" class="field pr-10">
                                    <button type="button" onclick="togglePassVisibility('new_password_confirmation', this)" class="absolute inset-y-0 right-0 px-3 flex items-center text-slate-400 hover:text-slate-700 cursor-pointer rounded-r-xl" title="Toggle password visibility" aria-label="Toggle password visibility">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl bg-paper/70 ring-1 ring-black/5 px-4 py-3.5">
                            <p class="text-[11px] font-bold uppercase tracking-[0.12em] text-slate-500">Password Requirements:</p>
                            <ul class="mt-2 space-y-1.5 text-[12.5px] text-slate-600">
                                @foreach(['Must be at least 8 characters in length', 'Must differ from your initial default password', 'Will be required for all future sign-ins to your account'] as $rule)
                                    <li class="flex items-start gap-2.5">
                                        <span class="mt-[0.45rem] w-1 h-1 rotate-45 bg-gold shrink-0" aria-hidden="true"></span>
                                        <span>{{ $rule }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="pt-1">
                            <button type="submit" class="btn btn-primary w-full sm:w-auto">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Save New Password
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <!-- SECONDARY: Official Profile Summary & Phone (order-2 on mobile, sidebar on desktop) -->
        <div class="lg:col-span-1 order-2 lg:order-1 space-y-6">
            <!-- Profile Identity Card -->
            <section class="card overflow-hidden" aria-label="Profile summary">
                <div class="card-body text-center pt-7">
                    <span class="w-16 h-16 mx-auto rounded-full grid place-items-center font-display text-[1.6rem] text-white"
                          style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px #fff, 0 0 0 4px rgb(184 137 43 / .45), 0 10px 22px -8px rgb(165 28 48 / .55);"
                          aria-hidden="true">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                    <h2 class="mt-4 font-display text-[1.25rem] leading-tight text-slate-900">{{ $user->name }}</h2>
                    <span class="badge badge-brand mt-2">{{ $user->getRoleDisplayName() }}</span>
                </div>

                <dl class="border-t border-slate-100 px-5 py-4 space-y-3.5 text-[13px]">
                    <div>
                        <dt class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-slate-400">Official Email</dt>
                        <dd class="mt-0.5 text-slate-800 font-medium break-all">{{ $user->email }}</dd>
                    </div>

                    @if($user->club)
                        <div>
                            <dt class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-slate-400">Assigned Fellowship</dt>
                            <dd class="mt-0.5 text-slate-800 font-medium">{{ $user->club?->name }} ({{ $user->club?->code }})</dd>
                        </div>
                    @endif

                    @if($user->position)
                        <div>
                            <dt class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-slate-400">Official Position</dt>
                            <dd class="mt-0.5 text-slate-800 font-medium">{{ $user->position }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-[10.5px] font-bold uppercase tracking-[0.12em] text-slate-400">Account Status</dt>
                        <dd class="mt-1">
                            <span class="badge badge-emerald badge-dot capitalize">{{ $user->status ?? 'Active' }}</span>
                        </dd>
                    </div>
                </dl>
            </section>

            <!-- Contact Phone Form -->
            <section class="card" aria-labelledby="contact-heading">
                <div class="card-header">
                    <h3 id="contact-heading" class="card-title">Contact Phone</h3>
                    <span class="text-[11px] text-slate-400">Optional</span>
                </div>
                <form method="POST" action="{{ route('profile.update') }}" class="card-body space-y-3">
                    @csrf
                    <div>
                        <label for="profile_phone" class="field-label">Mobile / WhatsApp</label>
                        <input type="text" id="profile_phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+263 77 123 4567" class="field">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-block">
                        Update Contact
                    </button>
                </form>
            </section>
        </div>

    </div>

</div>

<script>
    function togglePassVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            btn.classList.add('text-brand');
        } else {
            input.type = 'password';
            btn.classList.remove('text-brand');
        }
    }
</script>
@endsection
