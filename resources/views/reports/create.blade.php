@extends('layouts.app')

@section('title', 'Submit Post-Activity Report')

@section('content')
<div class="page-shell max-w-3xl space-y-6">

    <x-page-header eyebrow="Chapel Manual §7.1.2"
                   title="Post-Activity Report"
                   description="Submit formal evaluation, financial expenditure, and photos following an approved chapel activity.">
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[12px] text-slate-500">
            <a href="{{ route('reports.index') }}" class="font-semibold text-brand hover:text-brand-dark">Reports</a>
            <span class="text-slate-300" aria-hidden="true">/</span>
            <span class="text-slate-700" aria-current="page">New Report</span>
        </nav>
    </x-page-header>

    <section class="card">
        <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="card-body sm:p-8 space-y-6">
                {{-- The activity --}}
                <fieldset class="space-y-5">
                    <legend class="card-kicker mb-4">The activity</legend>

                    <div>
                        <label for="report_event_id" class="field-label">Linked Calendar Event</label>
                        <select id="report_event_id" name="event_id" class="field">
                            <option value="">-- Standalone or Select Completed Event --</option>
                            @foreach($events as $ev)
                                <option value="{{ $ev->id }}" {{ $selectedEventId == $ev->id ? 'selected' : '' }}>
                                    {{ $ev->event_date->format('Y-m-d') }} &bull; {{ $ev->title }} ({{ $ev->club?->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="report_title" class="field-label">Activity Report Title <span class="text-brand" aria-hidden="true">*</span></label>
                        <input id="report_title" type="text" name="title" required placeholder="e.g. FOCUS Semester Discipleship Summit Final Report" class="field">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="report_attendance" class="field-label">Actual Attendance Count <span class="text-brand" aria-hidden="true">*</span></label>
                            <input id="report_attendance" type="number" name="attendance" min="1" required placeholder="e.g. 145" class="field tabular-nums">
                        </div>
                        <div>
                            <label for="report_spending" class="field-label">Total Spending (USD) <span class="text-brand" aria-hidden="true">*</span></label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[13px] text-slate-400" aria-hidden="true">$</span>
                                <input id="report_spending" type="number" step="0.01" name="spending" min="0" required placeholder="e.g. 240.50" class="field pl-7 tabular-nums">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="rule-ornament text-[8px]" aria-hidden="true">◆</div>

                {{-- The witness --}}
                <fieldset class="space-y-5">
                    <legend class="card-kicker mb-4">Outcome &amp; evidence</legend>

                    <div>
                        <label for="report_outcome" class="field-label">Outcomes &amp; Spiritual Impact Narrative <span class="text-brand" aria-hidden="true">*</span></label>
                        <textarea id="report_outcome" name="outcome" rows="6" required placeholder="Describe the execution of the activity, number of attendees, spiritual testimonies, and any challenges encountered..." class="field leading-relaxed"></textarea>
                    </div>

                    <div>
                        <label for="report_photos" class="field-label">Activity Photos / Receipts (Up to 5 images)</label>
                        <input id="report_photos" type="file" name="photos[]" multiple accept="image/*" aria-describedby="report_photos_hint"
                               class="field py-2 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-[12px] file:font-semibold file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer">
                        <p id="report_photos_hint" class="field-hint">Supported formats: JPG, PNG. Max 5MB per photo.</p>
                    </div>
                </fieldset>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 px-5 sm:px-8 py-4 border-t border-slate-100 bg-slate-50/60 rounded-b-[var(--radius-card)]">
                <a href="{{ route('reports.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Report</button>
            </div>
        </form>
    </section>

</div>
@endsection
