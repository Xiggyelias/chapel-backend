@extends('layouts.app')

@section('title', 'Register a Fellowship or Chapel Club')

@section('content')
<div class="page-shell max-w-4xl space-y-6">

    <header class="page-header">
        <div class="min-w-0">
            <nav aria-label="Breadcrumb" class="eyebrow">
                <a href="{{ route('dashboard') }}" class="hover:underline underline-offset-4">Dashboard</a>
                <span class="text-slate-300" aria-hidden="true">/</span>
                <a href="{{ route('clubs.index') }}" class="hover:underline underline-offset-4">Fellowships Registry</a>
                <span class="text-slate-300" aria-hidden="true">/</span>
                <span class="text-slate-500" aria-current="page">New Fellowship Charter</span>
            </nav>
            <h1 class="page-title">Register a Fellowship or Chapel Ministry</h1>
            <p class="page-lede">Apply for official chartering and portal access under Chaplaincy oversight (Chapel Manual §2.3)</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('clubs.index') }}" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Registry
            </a>
        </div>
    </header>

    <!-- Institutional Guidelines Protocol Card -->
    <section class="card overflow-hidden" aria-labelledby="protocol-heading">
        <div class="card-body flex flex-col sm:flex-row gap-5">
            <span class="w-11 h-11 rounded-full grid place-items-center shrink-0 text-gold bg-gold-soft ring-1 ring-gold/25" aria-hidden="true">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 id="protocol-heading" class="card-title">Fellowship Chartering Protocol</h2>
                    <span class="badge badge-gold font-mono">Chapel Manual §2.3</span>
                </div>
                <p class="mt-2 text-[13px] text-slate-600 leading-relaxed">
                    All student religious fellowships, choir groups, and ministry societies must maintain active chartering through the Chaplaincy Office. Newly registered groups receive permanent records vault storage, master calendar booking rights, and official representation on the Chapel Committee.
                </p>
                <ul class="mt-4 flex flex-wrap gap-x-5 gap-y-2 text-[12px] font-medium text-slate-700">
                    @foreach(['Permanent Vault Retention', 'Sanctuary & Venue Access', 'Chapel Committee Seat'] as $benefit)
                        <li class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 13l4 4L19 7"/></svg>
                            {{ $benefit }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    @if ($errors->any())
        <div class="flash-error" role="alert">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div class="text-[12.5px]">
                <p class="font-semibold">Please correct the errors below:</p>
                <ul class="mt-1 list-disc pl-4 space-y-0.5 opacity-90">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Registration Form -->
    <section class="card" aria-label="Charter application form">
        <form method="POST" action="{{ route('clubs.store') }}" enctype="multipart/form-data" class="divide-y divide-slate-100">
            @csrf

            <!-- Section 1: Fellowship Identity -->
            <fieldset class="min-w-0 p-5 sm:p-7 grid grid-cols-1 md:grid-cols-[13rem_minmax(0,1fr)] gap-x-8 gap-y-5">
                <legend class="sr-only">1. Fellowship Identity &amp; Categorization</legend>
                <div aria-hidden="true">
                    <span class="font-display text-[1.75rem] leading-none text-gold">I</span>
                    <h3 class="mt-2 card-title">Fellowship Identity &amp; Categorization</h3>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Official university name, classification, and ministry scope</p>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label for="reg_name" class="field-label">Official Fellowship Name <span class="text-brand">*</span></label>
                            <input type="text" id="reg_name" name="name" value="{{ old('name') }}" required placeholder="e.g. Anglican Student Society" class="field">
                        </div>
                        <div>
                            <label for="reg_code" class="field-label">Acronym / Short Code <span class="text-brand">*</span></label>
                            <input type="text" id="reg_code" name="code" value="{{ old('code') }}" required placeholder="e.g. ANGLICAN" class="field font-mono uppercase">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="reg_type" class="field-label">Ministry Classification <span class="text-brand">*</span></label>
                            <select id="reg_type" name="type" required class="field">
                                <option value="">-- Select Category --</option>
                                <option value="fellowship" {{ old('type') == 'fellowship' ? 'selected' : '' }}>Campus Fellowship / Denominational Movement</option>
                                <option value="prayer group" {{ old('type') == 'prayer group' ? 'selected' : '' }}>Intercessory Prayer & Revival Group</option>
                                <option value="choir or worship team" {{ old('type') == 'choir or worship team' ? 'selected' : '' }}>Choral Ensemble or Worship Team</option>
                                <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Humanitarian, Volunteer & Service Ministry</option>
                            </select>
                        </div>
                        <div>
                            <label for="reg_official_email" class="field-label">Official Club Email <span class="text-brand">*</span></label>
                            <input type="email" id="reg_official_email" name="official_email" value="{{ old('official_email') }}" required placeholder="e.g. anglican@africau.edu" class="field">
                        </div>
                    </div>

                    <div>
                        <label for="reg_description" class="field-label">Purpose & Statement of Ministry <span class="text-brand">*</span></label>
                        <textarea id="reg_description" name="description" rows="4" required placeholder="Detail the core biblical mission, aims, and anticipated impact of this fellowship within the Kwang-Lim Chapel community..." class="field leading-relaxed">{{ old('description') }}</textarea>
                    </div>
                </div>
            </fieldset>

            <!-- Section 2: Meeting Schedule & Venue -->
            <fieldset class="min-w-0 p-5 sm:p-7 grid grid-cols-1 md:grid-cols-[13rem_minmax(0,1fr)] gap-x-8 gap-y-5">
                <legend class="sr-only">2. Proposed Schedule &amp; Preferred Campus Venue</legend>
                <div aria-hidden="true">
                    <span class="font-display text-[1.75rem] leading-none text-gold">II</span>
                    <h3 class="mt-2 card-title">Proposed Schedule &amp; Preferred Campus Venue</h3>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Regular campus gathering hours subject to Assistant Chaplain coordination</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 content-start">
                    <div>
                        <label for="reg_meeting_schedule" class="field-label">Proposed Meeting Schedule <span class="text-brand">*</span></label>
                        <input type="text" id="reg_meeting_schedule" name="meeting_schedule" value="{{ old('meeting_schedule') }}" required placeholder="e.g. Mondays 17:30 - 19:00" class="field">
                    </div>
                    <div>
                        <label for="reg_venue" class="field-label">Preferred Campus Venue <span class="text-brand">*</span></label>
                        <select id="reg_venue" name="venue" required class="field">
                            <option value="">-- Select Preferred Venue --</option>
                            @foreach($venues as $v)
                                <option value="{{ $v->name }}" {{ old('venue') == $v->name ? 'selected' : '' }}>
                                    {{ $v->name }} (Capacity: {{ $v->capacity }})
                                </option>
                            @endforeach
                            <option value="Old Chapel Room" {{ old('venue') == 'Old Chapel Room' ? 'selected' : '' }}>Old Chapel Room</option>
                            <option value="Prayer Garden" {{ old('venue') == 'Prayer Garden' ? 'selected' : '' }}>Prayer Garden</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Section 3: Executive Leadership & Governance -->
            <fieldset class="min-w-0 p-5 sm:p-7 grid grid-cols-1 md:grid-cols-[13rem_minmax(0,1fr)] gap-x-8 gap-y-5">
                <legend class="sr-only">3. Founding Executive Leadership &amp; Constitution</legend>
                <div aria-hidden="true">
                    <span class="font-display text-[1.75rem] leading-none text-gold">III</span>
                    <h3 class="mt-2 card-title">Founding Executive Leadership &amp; Constitution</h3>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Contact information for initial portal provision and permanent vault archive</p>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="reg_president_name" class="field-label">Founding President Name <span class="text-brand">*</span></label>
                            <input type="text" id="reg_president_name" name="founding_president_name" value="{{ old('founding_president_name') }}" required placeholder="e.g. Farai Makoni" class="field">
                        </div>
                        <div>
                            <label for="reg_president_email" class="field-label">President Official Email <span class="text-brand">*</span></label>
                            <input type="email" id="reg_president_email" name="founding_president_email" value="{{ old('founding_president_email') }}" required placeholder="e.g. fmakoni@africau.edu" class="field">
                        </div>
                        <div class="sm:col-span-2 sm:max-w-[calc(50%-0.5rem)]">
                            <label for="reg_contact_phone" class="field-label">WhatsApp / Phone Contact <span class="text-brand">*</span></label>
                            <input type="text" id="reg_contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" required placeholder="e.g. +263 77 123 4567" class="field">
                        </div>
                    </div>

                    <div>
                        <label for="reg_constitution" class="field-label">Draft Constitution / Bye-Laws (PDF or DOC)</label>
                        <input type="file" id="reg_constitution" name="constitution" accept=".pdf,.doc,.docx" aria-describedby="reg_constitution_hint"
                               class="field !py-2 text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-vellum file:px-3 file:py-1.5 file:text-[12px] file:font-semibold file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer">
                        <p id="reg_constitution_hint" class="field-hint">Optional during application. An approved constitution is required for complete Vault record compliance.</p>
                    </div>
                </div>
            </fieldset>

            <!-- Submission Actions -->
            <div class="p-5 sm:px-7 bg-slate-50/70 rounded-b-[var(--radius-card)] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <p class="text-[12px] text-slate-500 italic max-w-sm">
                    By submitting, the executive leadership agrees to adhere to the Chapel Manual and ministry guidelines.
                </p>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('clubs.index') }}" class="btn btn-ghost flex-1 sm:flex-none">Cancel</a>
                    <button type="submit" class="btn btn-primary flex-1 sm:flex-none">
                        Submit Charter Application
                    </button>
                </div>
            </div>
        </form>
    </section>

</div>
@endsection
