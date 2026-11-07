@extends('layouts.app')

@section('title', 'Members & Attendance')

@section('content')
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Roster & attendance" title="{{ $selectedClub->name }} Members & Attendance"
                   description="Managed securely by {{ $selectedClub->code }} executives. Confidential student contact details.">
        @if(!auth()->user()->isCampusLifeSrc())
            <a href="{{ route('members.qr_kiosk', $selectedClub->id) }}" target="_blank" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                QR Check-In Kiosk
            </a>
            <a href="{{ route('members.scanner', $selectedClub->id) }}" target="_blank" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Camera Scanner
            </a>
            <button type="button" onclick="document.getElementById('takeAttendanceModal').classList.toggle('hidden')" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Manual Roll Call
            </button>
            <button type="button" onclick="document.getElementById('newMemberModal').classList.toggle('hidden')" class="btn btn-ink">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"/></svg>
                Add Member
            </button>
        @endif
    </x-page-header>

    {{-- Roster context + privacy standard --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <section class="card card-body">
            <p class="card-kicker">Managing roster for</p>
            <div class="flex items-center gap-2.5 mt-2">
                <span class="w-3 h-3 rounded-full shrink-0 ring-4 ring-slate-100" style="background-color: {{ $selectedClub->color_hex ?? '#DC2626' }}" aria-hidden="true"></span>
                <span class="card-title text-[1.125rem]">{{ $selectedClub->name }}</span>
            </div>
            @if(auth()->user()->isOffice())
                <form method="GET" action="{{ route('members.index') }}" class="pt-4">
                    <label for="members_switch_club" class="sr-only">Switch club</label>
                    <select id="members_switch_club" name="club_id" onchange="this.form.submit()" class="field py-2">
                        @foreach($clubs as $c)
                            <option value="{{ $c->id }}" {{ $selectedClub->id == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                        @endforeach
                    </select>
                </form>
            @endif
        </section>

        <section class="card card-body md:col-span-2 flex gap-4">
            <span class="grid place-items-center w-10 h-10 rounded-full bg-gold-soft text-gold ring-1 ring-gold/25 shrink-0" aria-hidden="true">
                <svg class="w-[1.125rem] h-[1.125rem]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </span>
            <div class="min-w-0">
                <p class="card-kicker">§4.7 &amp; §10</p>
                <h2 class="card-title mt-0.5">Privacy &amp; confidentiality standard</h2>
                <p class="mt-1.5 text-[13px] leading-relaxed text-slate-600">
                    Club leaders maintain their own member lists and meeting attendance. Member contact details are strictly confidential and visible <strong class="font-semibold text-slate-800">only to this club's leaders and the Chaplaincy Office</strong>. Cross-club access is strictly restricted by institutional security policies.
                </p>
            </div>
        </section>
    </div>

    {{-- Roster --}}
    <section class="card overflow-hidden">
        <div class="card-header">
            <h2 class="card-title">Club membership roster</h2>
            <span class="text-[12px] text-slate-500"><span class="font-semibold text-slate-800 tabular-nums">{{ $members->total() }}</span> Active Members</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Full name</th>
                        <th>Student ID</th>
                        <th>Club role</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th class="text-right">Meetings attended</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $m)
                        <tr>
                            <td class="font-semibold text-slate-900 whitespace-nowrap">{{ $m->full_name }}</td>
                            <td class="font-mono text-[12px] text-slate-600">{{ $m->student_id ?: 'N/A' }}</td>
                            <td class="whitespace-nowrap"><span class="badge">{{ $m->role_in_club }}</span></td>
                            <td class="text-slate-600">{{ $m->email ?: '—' }}</td>
                            <td class="text-slate-600 tabular-nums whitespace-nowrap">{{ $m->phone ?: '—' }}</td>
                            <td class="whitespace-nowrap"><span class="badge badge-emerald badge-dot capitalize">{{ $m->status }}</span></td>
                            <td class="text-right whitespace-nowrap">
                                <span class="font-display text-[1.125rem] text-slate-900 tabular-nums">{{ $m->present_count }}</span>
                                <span class="text-[12.5px] text-slate-500 ml-0.5">meetings</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="!p-0">
                                <div class="empty-state">
                                    <div class="empty-state-mark" aria-hidden="true">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <p class="empty-state-title">No members enrolled yet</p>
                                    <p class="empty-state-text">Add student members to track roll calls, attendances, and check-in kiosks.</p>
                                    @if(!auth()->user()->isCampusLifeSrc())
                                        <button type="button" onclick="document.getElementById('newMemberModal').classList.toggle('hidden')" class="btn btn-primary btn-sm mt-4">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"/></svg>
                                            Add First Member
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
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $members->links() }}
            </div>
        @endif
    </section>

</div>

{{-- Add member --}}
<div id="newMemberModal" role="dialog" aria-modal="true" aria-labelledby="newMemberTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full ring-1 ring-black/5 overflow-hidden modal-panel max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="px-6 pt-6 pb-4 flex items-start justify-between gap-4">
            <div>
                <p class="eyebrow">{{ $selectedClub->code }} roster</p>
                <h3 id="newMemberTitle" class="font-display text-xl text-slate-900">Add club member</h3>
            </div>
            <button type="button" onclick="document.getElementById('newMemberModal').classList.add('hidden')" class="topbar-action -mr-2 -mt-1" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('members.store') }}" class="px-6 pb-6 space-y-4">
            @csrf
            <input type="hidden" name="club_id" value="{{ $selectedClub->id }}">

            <div>
                <label for="member_full_name" class="field-label">Full name <span class="text-brand">*</span></label>
                <input type="text" id="member_full_name" name="full_name" required placeholder="e.g. Takudzwa Moyo" class="field">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="member_student_id" class="field-label">Student ID</label>
                    <input type="text" id="member_student_id" name="student_id" placeholder="e.g. AU2024-890" class="field">
                </div>
                <div>
                    <label for="member_role" class="field-label">Role in club <span class="text-brand">*</span></label>
                    <select id="member_role" name="role_in_club" required class="field">
                        <option value="Member">Member</option>
                        <option value="Secretary">Secretary</option>
                        <option value="Treasurer">Treasurer</option>
                        <option value="Committee">Committee Member</option>
                        <option value="Prayer Lead">Prayer Lead</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="member_email" class="field-label">Email</label>
                <input type="email" id="member_email" name="email" placeholder="student@africau.edu" class="field">
            </div>

            <div>
                <label for="member_phone" class="field-label">Phone</label>
                <input type="text" id="member_phone" name="phone" placeholder="+263 77 ..." class="field">
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newMemberModal').classList.add('hidden')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Member</button>
            </div>
        </form>
    </div>
</div>

{{-- Manual roll call --}}
<div id="takeAttendanceModal" role="dialog" aria-modal="true" aria-labelledby="takeAttendanceTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop">
    <div class="bg-white rounded-3xl shadow-lg max-w-lg w-full ring-1 ring-black/5 overflow-hidden modal-panel max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="px-6 pt-6 pb-4 flex items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Manual roll call</p>
                <h3 id="takeAttendanceTitle" class="font-display text-xl text-slate-900">Record weekly meeting attendance</h3>
            </div>
            <button type="button" onclick="document.getElementById('takeAttendanceModal').classList.add('hidden')" class="topbar-action -mr-2 -mt-1" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('members.attendance') }}" class="px-6 pb-6 space-y-4">
            @csrf
            <input type="hidden" name="club_id" value="{{ $selectedClub->id }}">

            <div>
                <label for="attendance_meeting_date" class="field-label">Meeting date <span class="text-brand">*</span></label>
                <input type="date" id="attendance_meeting_date" name="meeting_date" value="{{ date('Y-m-d') }}" required class="field">
            </div>

            <div class="max-h-60 overflow-y-auto divide-y divide-slate-100 rounded-2xl ring-1 ring-slate-200/80 px-4">
                @forelse($members as $m)
                    <fieldset class="py-3 flex items-center justify-between gap-4">
                        <legend class="sr-only">{{ $m->full_name }}</legend>
                        <div class="min-w-0">
                            <span class="text-[13px] font-semibold text-slate-900 block truncate">{{ $m->full_name }}</span>
                            <span class="text-[12px] text-slate-500 block">{{ $m->role_in_club }}</span>
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="attendance[{{ $m->id }}]" value="present" checked>
                                <span class="text-[12px] font-semibold text-emerald-700">Present</span>
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="attendance[{{ $m->id }}]" value="absent">
                                <span class="text-[12px] font-semibold text-rose-700">Absent</span>
                            </label>
                        </div>
                    </fieldset>
                @empty
                    <div class="py-6 text-center text-[13px] text-slate-400">Add members to roster first.</div>
                @endforelse
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('takeAttendanceModal').classList.add('hidden')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Attendance Register</button>
            </div>
        </form>
    </div>
</div>
@endsection
