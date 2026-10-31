@extends('layouts.app')

@section('title', 'Staff & User Registry')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">
                <span>Chaplaincy Administration</span>
                <span>&bull;</span>
                <span class="text-[#DC2626]">User Governance</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Staff &amp; Executive User Registry
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Manage and provision portal accounts for ministers, assistant chaplains, and student fellowship executives.
            </p>
        </div>

        @if($currentUser->isChapelSecretary() || $currentUser->isChaplain())
            <button type="button" onclick="document.getElementById('registerUserModal').classList.remove('hidden')" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider shadow-sm transition active:scale-95 shrink-0">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Register New Staff or Executive</span>
            </button>
        @endif
    </div>

    <!-- Quick Role Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total System Users</span>
            <div class="text-3xl font-extrabold text-slate-900 mt-1">{{ $stats['total_users'] }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Active login credentials</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#DC2626]">Chapel Staff</span>
            <div class="text-3xl font-extrabold text-[#DC2626] mt-1">{{ $stats['chapel_staff'] }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Chaplain, Assistant Chaplain, Secretary</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Fellowship Leaders</span>
            <div class="text-3xl font-extrabold text-emerald-700 mt-1">{{ $stats['club_leaders'] }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Club Presidents &amp; Executives</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">Campus Observers</span>
            <div class="text-3xl font-extrabold text-blue-700 mt-1">{{ $stats['campus_life'] }}</div>
            <div class="text-xs text-slate-500 mt-0.5">Campus Life &amp; SRC</div>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        <!-- Search and Filter Bar -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or role..." class="px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] w-full sm:w-64">
                
                <select name="role" onchange="this.form.submit()" class="px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-700">
                    <option value="">All Roles</option>
                    <option value="chaplain" {{ request('role') == 'chaplain' ? 'selected' : '' }}>Chaplain</option>
                    <option value="chapel_assistant" {{ request('role') == 'chapel_assistant' ? 'selected' : '' }}>Assistant Chaplain</option>
                    <option value="chapel_secretary" {{ request('role') == 'chapel_secretary' ? 'selected' : '' }}>Chapel Secretary</option>
                    <option value="club_president" {{ request('role') == 'club_president' ? 'selected' : '' }}>Club President</option>
                    <option value="club_executive" {{ request('role') == 'club_executive' ? 'selected' : '' }}>Club Executive</option>
                    <option value="campus_life_src" {{ request('role') == 'campus_life_src' ? 'selected' : '' }}>Campus Life / SRC</option>
                </select>

                <button type="submit" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'role']))
                    <a href="{{ route('users.index') }}" class="text-xs font-semibold text-[#DC2626] hover:underline px-2">Clear</a>
                @endif
            </form>

            <span class="text-xs text-slate-500 whitespace-nowrap">
                Showing <strong>{{ $users->total() }}</strong> accounts
            </span>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase font-bold text-[10px] tracking-wider">
                    <tr>
                        <th class="px-6 py-3.5">User &amp; Position</th>
                        <th class="px-6 py-3.5">Official Email</th>
                        <th class="px-6 py-3.5">Assigned Role</th>
                        <th class="px-6 py-3.5">Club Affiliation</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $u->position ?: $u->getRoleDisplayName() }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-mono text-slate-600">
                                {{ $u->email }}
                                @if($u->phone)
                                    <div class="text-[10px] text-slate-400 font-sans">{{ $u->phone }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($u->isChaplain())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-[#DC2626]">Chaplain</span>
                                @elseif($u->isChapelAssistant())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Assistant Chaplain</span>
                                @elseif($u->isChapelSecretary())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">Secretary / Admin</span>
                                @elseif($u->isClubPresident())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Club President</span>
                                @elseif($u->isClubExecutive())
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800">Club Executive</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Campus Life / SRC</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($u->club)
                                    <span class="font-semibold text-slate-800">{{ $u->club?->name }}</span>
                                    <span class="text-[10px] font-mono text-slate-400 block">{{ $u->club?->code }}</span>
                                @else
                                    <span class="text-slate-400">Chapel Office</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                @if($u->status === 'active')
                                    <span class="inline-flex items-center space-x-1 text-emerald-700 font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        <span>Active</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 text-rose-700 font-semibold text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        <span>Suspended</span>
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if($currentUser->isChapelSecretary() || $currentUser->isChaplain())
                                    <div class="inline-flex items-center space-x-2">
                                        <form method="POST" action="{{ route('users.reset_password', $u->id) }}" onsubmit="return confirm('Issue a new temporary password for this account? Their current password will stop working.');">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-medium transition" title="Issue a temporary password">
                                                Reset Password
                                            </button>
                                        </form>

                                        @if($u->id !== $currentUser->id)
                                            <form method="POST" action="{{ route('users.toggle_status', $u->id) }}">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg {{ $u->status === 'active' ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} text-[11px] font-medium transition">
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
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                No user accounts matching the specified filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Register New Staff or Executive -->
<div id="registerUserModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 relative animate-scale-up space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-bold text-slate-900">Register Staff or Club Executive</h3>
                <p class="text-xs text-slate-500">Create official portal credentials</p>
            </div>
            <button type="button" onclick="document.getElementById('registerUserModal').classList.add('hidden')" class="p-1 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Full Legal Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Pastor Micah David or Grace Musonda" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Official Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="e.g. mdavid@africau.edu" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Phone / WhatsApp</label>
                    <input type="text" name="phone" placeholder="e.g. +263 77 123 4567" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Assigned Role <span class="text-rose-500">*</span></label>
                    <select name="role" id="roleSelect" required onchange="handleRoleChange(this.value)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-800">
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
                    <label class="block font-semibold text-slate-700 mb-1">Position Title</label>
                    <input type="text" name="position" placeholder="e.g. Associate Chaplain, General Secretary" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
            </div>

            <!-- Club selection (conditional) -->
            <div id="clubSelectContainer" class="hidden">
                <label class="block font-semibold text-slate-700 mb-1">Assigned Student Fellowship <span class="text-rose-500">*</span></label>
                <select name="club_id" id="clubSelect" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] text-slate-800">
                    <option value="">-- Select Fellowship --</option>
                    @foreach($clubs as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->name }} ({{ $cl->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Initial Password (Optional)</label>
                <input type="text" name="password" placeholder="Leave blank to generate a secure temporary password" minlength="8" autocomplete="new-password" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                <span class="text-[10px] text-slate-400 mt-0.5 block">The account holder must replace this password at first sign-in.</span>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="document.getElementById('registerUserModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold tracking-wide uppercase shadow-sm transition active:scale-95">
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
