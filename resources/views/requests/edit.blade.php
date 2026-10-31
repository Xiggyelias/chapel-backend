@extends('layouts.app')

@section('title', 'Revise Activity Request')

@section('content')
@php
    $field = 'w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626] bg-white transition';
    $label = 'block text-xs font-semibold text-slate-700 mb-1.5';
    $categories = [
        'event' => 'Campus Event / Fellowship Activity',
        'venue' => 'Venue Booking Only',
        'poster' => 'Poster & Media Clearance',
        'equipment' => 'Chapel Equipment Loan',
        'outing' => 'Off-Campus Outing / Mission Trip (Requires VC Go-Ahead)',
    ];
@endphp
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('requests.index') }}" class="hover:underline text-[#DC2626] font-bold">Requests</a>
            <span>/</span>
            <a href="{{ route('requests.show', $requestItem->id) }}" class="hover:underline text-[#DC2626] font-bold">{{ $requestItem->reference_no }}</a>
            <span>/</span>
            <span>Revise</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">
            {{ $requestItem->status === 'needs_changes' ? 'Revise & Resubmit Proposal' : 'Edit Proposal' }}
        </h1>
        <p class="text-xs text-slate-500 mt-1">{{ $requestItem->club?->name }} &bull; Saving sends the proposal back to the Chaplaincy review queue.</p>
    </div>

    @if($requestItem->status === 'needs_changes' && $requestItem->decision_reason)
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs">
            <strong class="block text-amber-900 mb-1">Changes requested by the Chaplaincy Office</strong>
            <p class="text-amber-900 leading-relaxed">{{ $requestItem->decision_reason }}</p>
        </div>
    @endif

    @if($errors->any())
        <div role="alert" class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-900">
            <strong class="block mb-1">Please correct the following:</strong>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('requests.update', $requestItem->id) }}" enctype="multipart/form-data" onsubmit="this.querySelector('[type=submit]').disabled = true;" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-5">
        @csrf
        @method('PUT')

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

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="expected_attendance" class="{{ $label }}">Expected Attendance</label>
                <input type="number" id="expected_attendance" name="expected_attendance" min="1" value="{{ old('expected_attendance', $requestItem->expected_attendance ?: 1) }}" class="{{ $field }}">
            </div>
            <div>
                <label for="budget" class="{{ $label }}">Budget (USD)</label>
                <input type="number" id="budget" name="budget" step="0.01" min="0" value="{{ old('budget', $requestItem->budget) }}" class="{{ $field }}">
            </div>
            <div>
                <label for="attachment" class="{{ $label }}">Replace Attachment (optional)</label>
                <input type="file" id="attachment" name="attachment" accept=".pdf,.docx,.png,.jpg,.jpeg" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 bg-white">
                @if($requestItem->attachment_name)
                    <span class="text-[11px] text-slate-500 mt-1 block">Current: {{ $requestItem->attachment_name }}</span>
                @endif
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
            <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" name="sign_interpreter_required" value="1" @checked(old('sign_interpreter_required', $requestItem->sign_interpreter_required)) class="rounded text-[#DC2626] focus:ring-[#DC2626]">
                Sign-language interpreter required
            </label>
            <input type="text" name="sign_interpreter_name" aria-label="Interpreter name" maxlength="255" value="{{ old('sign_interpreter_name', $requestItem->sign_interpreter_name) }}" placeholder="Interpreter name (if arranged)" class="{{ $field }}">
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pt-2">
            <a href="{{ route('requests.show', $requestItem->id) }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold text-center transition">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] disabled:opacity-60 text-white text-xs font-bold transition shadow-sm">
                Save & Submit for Review
            </button>
        </div>
    </form>
</div>
@endsection
