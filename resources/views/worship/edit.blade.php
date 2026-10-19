@extends('layouts.app')

@section('title', 'Edit Service Sheet')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 animate-fade-in">

    <div>
        <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase mb-1">
            <a href="{{ route('worship.index') }}" class="hover:underline">Worship Planning</a>
            <span>&bull;</span>
            <span class="text-slate-400 font-bold">Edit Service Sheet</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold font-cinzel text-slate-900 tracking-tight">
            Edit Worship Service Sheet
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">
            Update liturgical assignments, preacher notes, or accessibility provisions.
        </p>
    </div>

    <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
        <form method="POST" action="{{ route('worship.update', $sheet->id) }}" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Service Date <span class="text-rose-600">*</span></label>
                    <input type="date" name="service_date" value="{{ old('service_date', $sheet->service_date->toDateString()) }}" required class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Service Type <span class="text-rose-600">*</span></label>
                    <select name="service_type" required class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                        <option value="Sunday 9:00 a.m." {{ $sheet->service_type === 'Sunday 9:00 a.m.' ? 'selected' : '' }}>Sunday 9:00 a.m. University Worship</option>
                        <option value="Wednesday 8:00 a.m." {{ $sheet->service_type === 'Wednesday 8:00 a.m.' ? 'selected' : '' }}>Wednesday 8:00 a.m. Midweek Chapel</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Preacher</label>
                    <input type="text" name="preacher" value="{{ old('preacher', $sheet->preacher) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Liturgist</label>
                    <input type="text" name="liturgist" value="{{ old('liturgist', $sheet->liturgist) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Theme / Sermon Title</label>
                <input type="text" name="theme" value="{{ old('theme', $sheet->theme) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Scripture Reading(s)</label>
                <input type="text" name="scripture" value="{{ old('scripture', $sheet->scripture) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
            </div>

            <!-- The United Methodist Hymnal (UMH) Interactive Lookup -->
            @include('worship.partials.hymn_picker')

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Hymns (Separate with semicolons)</label>
                <textarea name="hymns" rows="2" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">{{ old('hymns', $sheet->hymns) }}</textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Hymn & Praise Lyrics</label>
                    <span class="text-[11px] text-slate-500 font-medium">Used for media projection slides & bulletin</span>
                </div>
                <textarea name="lyrics" rows="5" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626] font-mono leading-relaxed" placeholder="Enter or paste hymn stanzas, praise chorus, and worship lyrics here...">{{ old('lyrics', $sheet->lyrics) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bible Reader</label>
                    <input type="text" name="bible_reader" value="{{ old('bible_reader', $sheet->bible_reader) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Choir</label>
                    <input type="text" name="choir" value="{{ old('choir', $sheet->choir) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Praise & Worship Team</label>
                    <input type="text" name="praise_worship_team" value="{{ old('praise_worship_team', $sheet->praise_worship_team) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Ushers</label>
                    <input type="text" name="ushers" value="{{ old('ushers', $sheet->ushers) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Media Contact</label>
                    <input type="text" name="media_contact" value="{{ old('media_contact', $sheet->media_contact) }}" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                </div>
            </div>

            <!-- Accessibility & Sign-Language Interpreter -->
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
                <div class="flex items-center space-x-2.5">
                    <input type="checkbox" id="sign_interpreter_required" name="sign_interpreter_required" value="1" {{ $sheet->sign_interpreter_required ? 'checked' : '' }} onchange="document.getElementById('sign_name_box').classList.toggle('hidden', !this.checked)" class="rounded text-[#DC2626] focus:ring-[#DC2626]">
                    <label for="sign_interpreter_required" class="text-xs font-bold text-slate-800">Assign Sign-Language Interpreter (Accessibility & Special Needs Ministry)</label>
                </div>
                <div id="sign_name_box" class="{{ $sheet->sign_interpreter_required ? '' : 'hidden' }} pt-1">
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Interpreter Name / Unit</label>
                    <input type="text" name="sign_interpreter_name" value="{{ old('sign_interpreter_name', $sheet->sign_interpreter_name) }}" placeholder="e.g. Sister Chipo / AU Special Needs Guild" class="w-full text-xs p-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626]">
                </div>
            </div>

            <!-- Worship Policy Checklist Status -->
            <div class="p-4 bg-red-50/50 rounded-2xl border border-red-200/80 space-y-2">
                <span class="font-bold text-[#DC2626] uppercase tracking-wider text-[11px] block">Worship Policy Status Confirmations</span>
                <div class="flex flex-wrap items-center gap-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="preacher_submitted_materials" value="1" {{ $sheet->preacher_submitted_materials ? 'checked' : '' }} class="rounded text-[#DC2626] focus:ring-[#DC2626]">
                        <span class="text-slate-800 font-medium">Preacher Submitted Materials (3 Days Rule)</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="media_notified" value="1" {{ $sheet->media_notified ? 'checked' : '' }} class="rounded text-[#DC2626] focus:ring-[#DC2626]">
                        <span class="text-slate-800 font-medium">Media Notified with Programme (2 Days Rule)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Call to Services</label>
                <textarea name="order_of_service_notes" rows="3" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">{{ old('order_of_service_notes', $sheet->order_of_service_notes) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Status</label>
                <select name="status" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#DC2626] focus:border-[#DC2626]">
                    <option value="draft" {{ $sheet->status === 'draft' ? 'selected' : '' }}>Draft (In Preparation)</option>
                    <option value="ready" {{ $sheet->status === 'ready' ? 'selected' : '' }}>Ready (Approved for Service)</option>
                    <option value="completed" {{ $sheet->status === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end space-x-3">
                <a href="{{ route('worship.show', $sheet->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 font-semibold text-slate-700 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white font-bold shadow-sm transition">Update Service Sheet</button>
            </div>
        </form>
    </div>

</div>
@endsection
