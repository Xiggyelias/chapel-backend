@extends('layouts.app')

@section('title', 'Submit Post-Activity Report')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('reports.index') }}" class="hover:underline text-[#DC2626] font-bold">Reports</a>
            <span>/</span>
            <span class="text-slate-700">New Report</span>
        </div>
        <h2 class="text-xl sm:text-2xl font-bold font-cinzel text-slate-900 tracking-tight">
            Post-Activity Report (Chapel Manual §7.1.2)
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Submit formal evaluation, financial expenditure, and photos following an approved chapel activity.
        </p>
    </div>

    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-8">
        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <!-- Linked Event -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Linked Calendar Event</label>
                <select name="event_id" class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                    <option value="">-- Standalone or Select Completed Event --</option>
                    @foreach($events as $ev)
                        <option value="{{ $ev->id }}" {{ $selectedEventId == $ev->id ? 'selected' : '' }}>
                            {{ $ev->event_date->format('Y-m-d') }} &bull; {{ $ev->title }} ({{ $ev->club->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Report Title -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Activity Report Title <span class="text-rose-600">*</span></label>
                <input type="text" name="title" required placeholder="e.g. FOCUS Semester Discipleship Summit Final Report" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <!-- Attendance & Spending -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Actual Attendance Count <span class="text-rose-600">*</span></label>
                    <input type="number" name="attendance" min="1" required placeholder="e.g. 145" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Total Spending (USD) <span class="text-rose-600">*</span></label>
                    <input type="number" step="0.01" name="spending" min="0" required placeholder="e.g. 240.50" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                </div>
            </div>

            <!-- Outcome -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Outcomes & Spiritual Impact Narrative <span class="text-rose-600">*</span></label>
                <textarea name="outcome" rows="4" required placeholder="Describe the execution of the activity, number of attendees, spiritual testimonies, and any challenges encountered..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]"></textarea>
            </div>

            <!-- Photos -->
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Activity Photos / Receipts (Up to 5 images)</label>
                <input type="file" name="photos[]" multiple accept="image/*" class="w-full text-xs p-2.5 border border-slate-300 rounded-xl bg-white">
                <span class="text-[10px] text-slate-400 mt-1 block">Supported formats: JPG, PNG. Max 5MB per photo.</span>
            </div>

            <div class="pt-4 border-t border-slate-200 flex justify-end space-x-2">
                <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 font-semibold text-slate-700 hover:bg-slate-200 transition">Cancel</a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white font-bold text-xs shadow-md active:scale-95">Submit Report</button>
            </div>
        </form>
    </div>

</div>
@endsection
