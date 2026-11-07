@extends('layouts.app')

@section('title', 'Revise Activity Request')

@section('content')
@php
    $field = 'field';
    $label = 'field-label';
    $categories = [
        'event' => 'Campus Event / Fellowship Activity',
        'venue' => 'Venue Booking Only',
        'poster' => 'Poster & Media Clearance',
        'equipment' => 'Chapel Equipment Loan',
        'outing' => 'Off-Campus Outing / Mission Trip (Requires VC Go-Ahead)',
    ];
    $sectionHead = 'grid gap-5 p-5 sm:p-7 lg:grid-cols-[13rem_minmax(0,1fr)] lg:gap-10';
@endphp
<div class="page-shell max-w-5xl space-y-6">

    <header class="page-header">
        <div class="min-w-0">
            <nav class="eyebrow" aria-label="Breadcrumb">
                <a href="{{ route('requests.index') }}" class="hover:underline underline-offset-4">Requests</a>
                <span class="text-slate-300" aria-hidden="true">/</span>
                <a href="{{ route('requests.show', $requestItem->id) }}" class="hover:underline underline-offset-4 tabular-nums">{{ $requestItem->reference_no }}</a>
                <span class="text-slate-300" aria-hidden="true">/</span>
                <span class="text-slate-500">Revise</span>
            </nav>
            <h1 class="page-title">
                {{ $requestItem->status === 'needs_changes' ? 'Revise & Resubmit Proposal' : 'Edit Proposal' }}
            </h1>
            <p class="page-lede">{{ $requestItem->club?->name }} &middot; Saving sends the proposal back to the Chaplaincy review queue.</p>
        </div>
    </header>

    @if($requestItem->status === 'needs_changes' && $requestItem->decision_reason)
        <aside class="card overflow-hidden" aria-label="Changes requested">
            <span class="absolute inset-y-0 left-0 w-[3px] bg-amber-500" aria-hidden="true"></span>
            <div class="card-body sm:px-7">
                <p class="card-kicker !text-amber-700">From the Chaplaincy</p>
                <p class="card-title mt-0.5">Changes requested by the Chaplaincy Office</p>
                <p class="mt-2.5 font-display text-[15px] leading-relaxed text-slate-800 whitespace-pre-line">{{ $requestItem->decision_reason }}</p>
            </div>
        </aside>
    @endif

    @if($errors->any())
        <div role="alert" class="flash-error">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v3.75m0 3.75h.008M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-[13px]">
                <strong class="block font-semibold mb-1">Please correct the following:</strong>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('requests.update', $requestItem->id) }}" enctype="multipart/form-data" onsubmit="this.querySelector('[type=submit]').disabled = true;" class="card">
        @csrf
        @method('PUT')

        <div class="divide-y divide-slate-100">

            <section class="{{ $sectionHead }}">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">I</p>
                    <h2 class="card-title mt-2">The activity</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Category, title and purpose.</p>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="{{ $label }}">Request Category</label>
                            <select name="category" id="category" required class="{{ $field }}">
                                @foreach($categories as $value => $text)
                                    <option value="{{ $value }}" @selected(old('category', $requestItem->category) === $value)>{{ $text }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="title" class="{{ $label }}">Activity Title</label>
                            <input type="text" id="title" name="title" maxlength="255" required value="{{ old('title', $requestItem->title) }}" class="{{ $field }}">
                        </div>
                    </div>

                    <div>
                        <label for="purpose" class="{{ $label }}">Purpose & Description</label>
                        <textarea id="purpose" name="purpose" rows="4" required class="{{ $field }}">{{ old('purpose', $requestItem->purpose) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="{{ $sectionHead }}">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">II</p>
                    <h2 class="card-title mt-2">Date &amp; venue</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">When and where the activity takes place.</p>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="event_date" class="{{ $label }}">Activity Date</label>
                            <input type="date" id="event_date" name="event_date" min="{{ date('Y-m-d') }}" required value="{{ old('event_date', $requestItem->event_date->toDateString()) }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="start_time" class="{{ $label }}">Start Time</label>
                            <input type="time" id="start_time" name="start_time" required value="{{ old('start_time', substr($requestItem->start_time, 0, 5)) }}" class="{{ $field }}">
                        </div>
                        <div>
                            <label for="end_time" class="{{ $label }}">End Time</label>
                            <input type="time" id="end_time" name="end_time" required value="{{ old('end_time', substr($requestItem->end_time, 0, 5)) }}" class="{{ $field }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="venue_id" class="{{ $label }}">Chapel Venue</label>
                            <select name="venue_id" id="venue_id" class="{{ $field }}">
                                <option value="">-- Other location (describe below) --</option>
                                @foreach($venues as $venue)
                                    <option value="{{ $venue->id }}" @selected((string) old('venue_id', $requestItem->venue_id) === (string) $venue->id)>{{ $venue->name }} (Cap. {{ $venue->capacity }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="venue_other" class="{{ $label }}">Alternative Location</label>
                            <input type="text" id="venue_other" name="venue_other" maxlength="255" value="{{ old('venue_other', $requestItem->venue_other) }}" placeholder="e.g. Honde Valley, Student Union Hall" class="{{ $field }}">
                        </div>
                    </div>
                </div>
            </section>

            <section class="{{ $sectionHead }}">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">III</p>
                    <h2 class="card-title mt-2">Logistics</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Numbers, budget and supporting document.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="expected_attendance" class="{{ $label }}">Expected Attendance</label>
                        <input type="number" id="expected_attendance" name="expected_attendance" min="1" value="{{ old('expected_attendance', $requestItem->expected_attendance ?: 1) }}" class="{{ $field }} tabular-nums">
                    </div>
                    <div>
                        <label for="budget" class="{{ $label }}">Budget (USD)</label>
                        <input type="number" id="budget" name="budget" step="0.01" min="0" value="{{ old('budget', $requestItem->budget) }}" class="{{ $field }} tabular-nums">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="attachment" class="{{ $label }}">Replace Attachment (optional)</label>
                        <input type="file" id="attachment" name="attachment" accept=".pdf,.docx,.png,.jpg,.jpeg" class="field py-2 text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-[12px] file:font-semibold file:text-slate-800 hover:file:bg-slate-200">
                        @if($requestItem->attachment_name)
                            <span class="field-hint block">Current: {{ $requestItem->attachment_name }}</span>
                        @endif
                    </div>
                </div>
            </section>

            <section class="{{ $sectionHead }}">
                <div>
                    <p class="font-display text-[1.75rem] leading-none text-gold-light">IV</p>
                    <h2 class="card-title mt-2">Accessibility</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Chapel Accessibility &amp; Special Needs.</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-black/5 space-y-3 self-start">
                    <label class="flex items-center gap-2.5 text-[13px] font-semibold text-slate-800 cursor-pointer">
                        <input type="checkbox" name="sign_interpreter_required" value="1" @checked(old('sign_interpreter_required', $requestItem->sign_interpreter_required)) class="rounded">
                        Sign-language interpreter required
                    </label>
                    <input type="text" name="sign_interpreter_name" aria-label="Interpreter name" maxlength="255" value="{{ old('sign_interpreter_name', $requestItem->sign_interpreter_name) }}" placeholder="Interpreter name (if arranged)" class="{{ $field }}">
                </div>
            </section>

        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 rounded-b-[var(--radius-card)] border-t border-slate-100 bg-slate-50/70 px-5 py-4 sm:px-7">
            <a href="{{ route('requests.show', $requestItem->id) }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">
                Save & Submit for Review
            </button>
        </div>
    </form>
</div>
@endsection
