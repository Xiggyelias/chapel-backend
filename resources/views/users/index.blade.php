@extends('layouts.app')

@section('title', 'Staff & User Registry')

@section('content')
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Chaplaincy Administration · User Governance" title="Staff & Executive User Registry"
                   description="Manage and provision portal accounts for ministers, assistant chaplains, and student fellowship executives.">
        @if($currentUser->isChapelSecretary() || $currentUser->isChaplain())
            <button type="button" onclick="document.getElementById('registerUserModal').classList.remove('hidden')" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m7-7H5"/></svg>
                Register New Staff or Executive
            </button>
        @endif
    </x-page-header>

    {{-- Accounts at a glance --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @foreach([
            ['Total System Users', $stats['total_users'], 'Active login credentials', 'var(--color-gold)', 'bg-gold'],
            ['Chapel Staff', $stats['chapel_staff'], 'Chaplain, Assistant Chaplain, Secretary', 'var(--color-brand)', 'bg-brand'],
            ['Fellowship Leaders', $stats['club_leaders'], 'Club Presidents & Executives', 'var(--color-gold)', 'bg-gold'],
            ['Campus Observers', $stats['campus_life'], 'Campus Life & SRC', 'var(--color-slate-300)', 'bg-slate-300'],
        ] as [$label, $value, $hint, $tone, $dot])
            <div class="dash-stat-card hover:!translate-y-0" style="--tone: {{ $tone }}">
                <span class="flex items-center gap-2 text-[10.5px] font-bold uppercase tracking-[0.14em] text-slate-500 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dot }}" aria-hidden="true"></span>
                    <span class="truncate">{{ $label }}</span>
                </span>
                <div class="mt-4 stat-value text-[2.4rem] text-slate-900">{{ $value }}</div>
                <div class="mt-2 text-[11.5px] text-slate-500 leading-snug">{{ $hint }}</div>
            </div>
        @endforeach
    </div>

    {{-- Registry --}}
    <section class="card overflow-hidden" aria-labelledby="accounts-heading">
        <div class="card-header flex-col items-stretch lg:flex-row lg:items-center gap-3">
            <div class="min-w-0">
                <p class="card-kicker">Portal accounts</p>
                <h2 id="accounts-heading" class="card-title mt-0.5">
                    Showing <span class="tabular-nums">{{ $users->total() }}</span> accounts
                </h2>
            </div>

            <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-2" role="search">
                <label for="userSearch" class="sr-only">Search accounts</label>
                <div class="relative w-full sm:w-64">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                    <input type="text" id="userSearch" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or role..." class="field !py-2 pl-9">
                </div>

                <label for="userRoleFilter" class="sr-only">Filter by role</label>
                <select name="role" id="userRoleFilter" onchange="this.form.submit()" class="field !py-2 !w-auto">
                    <option value="">All Roles</option>
                    <option value="chaplain" {{ request('role') == 'chaplain' ? 'selected' : '' }}>Chaplain</option>
                    <option value="chapel_assistant" {{ request('role') == 'chapel_assistant' ? 'selected' : '' }}>Assistant Chaplain</option>
                    <option value="chapel_secretary" {{ request('role') == 'chapel_secretary' ? 'selected' : '' }}>Chapel Secretary</option>
                    <option value="club_president" {{ request('role') == 'club_president' ? 'selected' : '' }}>Club President</option>
                    <option value="club_executive" {{ request('role') == 'club_executive' ? 'selected' : '' }}>Club Executive</option>
                    <option value="campus_life_src" {{ request('role') == 'campus_life_src' ? 'selected' : '' }}>Campus Life / SRC</option>
                </select>

                <button type="submit" class="btn btn-secondary">Filter</button>

                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('users.index') }}" class="btn btn-ghost">Clear</a>
                @endif
            </form>
        </div>

        <div class="table-wrap">
            <table class="table min-w-[56rem]">
                <thead>
                    <tr>
                        <th scope="col">User &amp; Position</th>
                        <th scope="col">Official Email</th>
                        <th scope="col">Assigned Role</th>
                        <th scope="col">Club Affiliation</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-vellum text-slate-700 grid place-items-center font-display text-[13px] shrink-0 ring-1 ring-black/5" aria-hidden="true">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                        <div class="text-[11.5px] text-slate-500">{{ $u->position ?: $u->getRoleDisplayName() }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="font-mono text-[12px] text-slate-600">{{ $u->email }}</div>
                                @if($u->phone)
                                    <div class="text-[11px] text-slate-400">{{ $u->phone }}</div>
                                @endif
                            </td>

                            <td class="text-slate-700 whitespace-nowrap">
                                @if($u->isChaplain())
                                    <span class="font-semibold text-brand-dark">Chaplain</span>
                                @elseif($u->isChapelAssistant())
                                    <span class="font-medium">Assistant Chaplain</span>
                                @elseif($u->isChapelSecretary())
                                    <span class="font-medium">Secretary / Admin</span>
                                @elseif($u->isClubPresident())
                                    <span class="font-medium">Club President</span>
                                @elseif($u->isClubExecutive())
                                    <span class="font-medium">Club Executive</span>
                                @else
                                    <span class="font-medium">Campus Life / SRC</span>
                                @endif
                            </td>

                            <td>
                                @if($u->club)
                                    <div class="font-medium text-slate-800">{{ $u->club?->name }}</div>
                                    <div class="text-[10.5px] font-mono text-slate-400">{{ $u->club?->code }}</div>
                                @else
                                    <span class="text-slate-400">Chapel Office</span>
                                @endif
                            </td>

                            <td>
                                @if($u->status === 'active')
                                    <span class="badge badge-emerald badge-dot">Active</span>
                                @else
                                    <span class="badge badge-rose badge-dot">Suspended</span>
                                @endif
                            </td>

                            <td class="text-right">
                                @if($currentUser->isChapelSecretary() || $currentUser->isChaplain())
                                    <div class="inline-flex items-center gap-1.5">
                                        <form method="POST" action="{{ route('users.reset_password', $u->id) }}" onsubmit="return confirm('Issue a new temporary password for this account? Their current password will stop working.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Issue a temporary password">
                                                Reset Password
                                            </button>
                                        </form>

                                        @if($u->id !== $currentUser->id)
                                            <form method="POST" action="{{ route('users.toggle_status', $u->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $u->status === 'active' ? 'btn-danger' : 'btn-secondary !text-emerald-800' }}">
                                                    {{ $u->status === 'active' ? 'Suspend' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!p-0">
                                <div class="empty-state">
                                    <div class="empty-state-mark" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-5.2-5.2M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/></svg>
                                    </div>
                                    <p class="empty-state-title">No accounts found</p>
                                    <p class="empty-state-text">No user accounts matching the specified filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </section>

</div>

<!-- Modal: Register New Staff or Executive -->
<div id="registerUserModal" role="dialog" aria-modal="true" aria-labelledby="registerUserTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/50 backdrop-blur-sm modal-backdrop">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-lg ring-1 ring-black/5 relative modal-panel max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="flex items-start justify-between gap-4 px-6 pt-6 pb-4 border-b border-slate-100">
            <div>
                <p class="eyebrow">Provision credentials</p>
                <h3 id="registerUserTitle" class="font-display text-[1.375rem] leading-tight text-slate-900">Register Staff or Club Executive</h3>
                <p class="mt-1 text-[12.5px] text-slate-500">Create official portal credentials</p>
            </div>
            <button type="button" onclick="document.getElementById('registerUserModal').classList.add('hidden')" class="p-1.5 -mr-1.5 rounded-lg text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="px-6 py-5 space-y-4">
            @csrf

            <div>
                <label for="ruName" class="field-label">Full Legal Name <span class="text-brand">*</span></label>
                <input type="text" id="ruName" name="name" required placeholder="e.g. Pastor Micah David or Grace Musonda" class="field">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="ruEmail" class="field-label">Official Email <span class="text-brand">*</span></label>
                    <input type="email" id="ruEmail" name="email" required placeholder="e.g. mdavid@africau.edu" class="field">
                </div>

                <div>
                    <label for="ruPhone" class="field-label">Phone / WhatsApp</label>
                    <input type="text" id="ruPhone" name="phone" placeholder="e.g. +263 77 123 4567" class="field">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="roleSelect" class="field-label">Assigned Role <span class="text-brand">*</span></label>
                    <select name="role" id="roleSelect" required onchange="handleRoleChange(this.value)" class="field">
                        <option value="">-- Select Role --</option>
                        <option value="chaplain">University Chaplain</option>
                        <option value="chapel_assistant">Assistant Chaplain (Calendar management, liturgy coordination, request reviews)</option>
                        <option value="chapel_secretary">Chapel Secretary (Chief Admin)</option>
                        <option value="club_president">Club President</option>
                        <option value="club_executive">Club Executive Officer</option>
                        <option value="campus_life_src">Campus Life / SRC Observer</option>
                    </select>
                </div>

                <div>
                    <label for="ruPosition" class="field-label">Position Title</label>
                    <input type="text" id="ruPosition" name="position" placeholder="e.g. Associate Chaplain, General Secretary" class="field">
                </div>
            </div>

            <!-- Club selection (conditional) -->
            <div id="clubSelectContainer" class="hidden">
                <label for="clubSelect" class="field-label">Assigned Student Fellowship <span class="text-brand">*</span></label>
                <select name="club_id" id="clubSelect" class="field">
                    <option value="">-- Select Fellowship --</option>
                    @foreach($clubs as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->name }} ({{ $cl->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="ruPassword" class="field-label">Initial Password (Optional)</label>
                <input type="text" id="ruPassword" name="password" placeholder="Leave blank to generate a secure temporary password" minlength="8" autocomplete="new-password" class="field">
                <p class="field-hint">The account holder must replace this password at first sign-in.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2">
                <button type="button" onclick="document.getElementById('registerUserModal').classList.add('hidden')" class="btn btn-ghost">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    Register &amp; Provision Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function handleRoleChange(role) {
        const container = document.getElementById('clubSelectContainer');
        const select = document.getElementById('clubSelect');
        if (role === 'club_president' || role === 'club_executive') {
            container.classList.remove('hidden');
            select.required = true;
        } else {
            container.classList.add('hidden');
            select.required = false;
        }
    }
</script>
@endsection
