@extends('layouts.app')

@section('title', 'Members & Attendance')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                <span>Kwang-Lim Chapel</span>
                <span>&bull;</span>
                <span class="text-slate-400 font-bold">Roster & Attendance</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                {{ $selectedClub->name }} Members & Attendance
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Managed securely by {{ $selectedClub->code }} executives. Confidential student contact details.
            </p>
        </div>

        @if(!auth()->user()->isCampusLifeSrc())
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('members.qr_kiosk', $selectedClub->id) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold transition shadow-sm flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>QR Check-In Kiosk</span>
                </a>
                <a href="{{ route('members.scanner', $selectedClub->id) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 text-xs font-bold transition shadow-xs border border-slate-300 flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Camera Scanner</span>
                </a>
                <button type="button" onclick="document.getElementById('takeAttendanceModal').classList.toggle('hidden')" class="px-3.5 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm active:scale-95">
                    Manual Roll Call
                </button>
                <button type="button" onclick="document.getElementById('newMemberModal').classList.toggle('hidden')" class="px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider transition shadow-md active:scale-95">
                    + Add Member
                </button>
            </div>
        @endif
    </div>

    <!-- Active Club Selector (for Office) & Privacy Badge -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-3xl card-elevation border border-slate-200/80">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Managing Roster For:</span>
            <div class="flex items-center space-x-2.5 mt-1.5">
                <span class="w-3.5 h-3.5 rounded-full shrink-0" style="background-color: {{ $selectedClub->color_hex ?? '#DC2626' }}"></span>
                <span class="font-bold text-slate-900 text-sm font-cinzel">{{ $selectedClub->name }}</span>
            </div>
            @if(auth()->user()->isOffice())
                <form method="GET" action="{{ route('members.index') }}" class="pt-2.5">
                    <select name="club_id" onchange="this.form.submit()" class="w-full text-xs p-2 rounded-xl border border-slate-300 bg-slate-50/50">
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}" {{ $selectedClub->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div class="p-5 rounded-3xl bg-gradient-to-br from-amber-50 to-amber-100/60 border border-amber-300 text-amber-950 text-xs space-y-1.5 col-span-2 card-elevation">
            <div class="flex items-center space-x-1.5 font-bold uppercase tracking-wider text-[11px] text-amber-900 font-cinzel">
                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Privacy & Confidentiality Standard (§4.7 & §10)</span>
            </div>
            <p class="leading-relaxed text-slate-700">
                Club leaders maintain their own member lists and meeting attendance. Member contact details are strictly confidential and visible <strong>only to this club's leaders and the Chaplaincy Office</strong>. Cross-club access is strictly restricted by institutional security policies.
            </p>
        </div>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">Club Membership Roster</h3>
            <span class="text-xs text-slate-500 font-semibold">{{ $members->total() }} Active Members</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold uppercase text-slate-600 font-cinzel">
                        <th class="p-4">Full Name</th>
                        <th class="p-4">Student ID</th>
                        <th class="p-4">Club Role</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Phone</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Meetings Attended</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($members as $m)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="p-4 font-bold text-slate-900">{{ $m->full_name }}</td>
                            <td class="p-4 font-mono text-slate-600">{{ $m->student_id ?: 'N/A' }}</td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="bg-slate-100 px-2.5 py-0.5 rounded-md font-semibold text-slate-700 text-[11px]">{{ $m->role_in_club }}</span>
                            </td>
                            <td class="p-4 text-slate-600">{{ $m->email ?: '—' }}</td>
                            <td class="p-4 text-slate-600 font-mono">{{ $m->phone ?: '—' }}</td>
                            <td class="p-4 whitespace-nowrap">
                                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2.5 py-0.5 rounded-full uppercase">{{ $m->status }}</span>
                            </td>
                            <td class="p-4 text-right font-black text-[#DC2626] whitespace-nowrap font-mono">
                                {{ $m->present_count }} meetings
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#DC2626] flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Members Enrolled Yet</h4>
                                    <p class="text-xs text-slate-400">Add student members to track roll calls, attendances, and check-in kiosks.</p>
                                    @if(!auth()->user()->isCampusLifeSrc())
                                        <button type="button" onclick="document.getElementById('newMemberModal').classList.toggle('hidden')" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs cursor-pointer">
                                            <span>+ Add First Member</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $members->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Add Member Modal -->
<div id="newMemberModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden card-elevation">
        <div class="bg-gradient-to-r from-[#B91C1C] to-[#DC2626] p-5 text-white flex items-center justify-between border-b border-white/10">
            <h3 class="text-base font-bold text-white font-cinzel">Add Club Member</h3>
            <button type="button" onclick="document.getElementById('newMemberModal').classList.add('hidden')" class="text-white/80 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('members.store') }}" class="p-6 space-y-4 text-xs">
            @csrf
            <input type="hidden" name="club_id" value="{{ $selectedClub->id }}">

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                <input type="text" name="full_name" required placeholder="e.g. Takudzwa Moyo" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Student ID</label>
                    <input type="text" name="student_id" placeholder="e.g. AU2024-890" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Role in Club <span class="text-rose-600">*</span></label>
                    <select name="role_in_club" required class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                        <option value="Member">Member</option>
                        <option value="Secretary">Secretary</option>
                        <option value="Treasurer">Treasurer</option>
                        <option value="Committee">Committee Member</option>
                        <option value="Prayer Lead">Prayer Lead</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Email</label>
                <input type="email" name="email" placeholder="student@africau.edu" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" placeholder="+263 77 ..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('newMemberModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white text-xs font-bold shadow-md active:scale-95">Add Member</button>
            </div>
        </form>
    </div>
</div>

<!-- Take Attendance Modal -->
<div id="takeAttendanceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full border border-slate-200 overflow-hidden card-elevation">
        <div class="bg-gradient-to-r from-[#B91C1C] to-[#DC2626] p-5 text-white flex items-center justify-between border-b border-white/10">
            <h3 class="text-base font-bold text-white font-cinzel">Record Weekly Meeting Attendance</h3>
            <button type="button" onclick="document.getElementById('takeAttendanceModal').classList.add('hidden')" class="text-white/80 hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('members.attendance') }}" class="p-6 space-y-4 text-xs">
            @csrf
            <input type="hidden" name="club_id" value="{{ $selectedClub->id }}">

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Meeting Date <span class="text-rose-600">*</span></label>
                <input type="date" name="meeting_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 border border-slate-200 rounded-2xl p-2.5">
                @forelse($members as $m)
                    <div class="py-2.5 px-1.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">{{ $m->full_name }}</span>
                            <span class="text-[10px] text-slate-500 block">{{ $m->role_in_club }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                <input type="radio" name="attendance[{{ $m->id }}]" value="present" checked class="text-[#DC2626] focus:ring-[#DC2626]">
                                <span class="text-emerald-700 font-bold text-xs">Present</span>
                            </label>
                            <label class="flex items-center space-x-1.5 cursor-pointer">
                                <input type="radio" name="attendance[{{ $m->id }}]" value="absent" class="text-[#DC2626] focus:ring-[#DC2626]">
                                <span class="text-rose-700 font-bold text-xs">Absent</span>
                            </label>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-slate-400">Add members to roster first.</div>
                @endforelse
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('takeAttendanceModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white text-xs font-bold shadow-md active:scale-95">Save Attendance Register</button>
            </div>
        </form>
    </div>
</div>
@endsection
