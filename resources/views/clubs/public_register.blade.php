<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#15121A">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Apply for Fellowship Charter | Africa University Kwang-Lim Chapel</title>

    @include('partials.head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-paper text-slate-800 antialiased selection:bg-brand selection:text-white">
@php
    $sections = [
        ['sec-leadership', 'I', 'Leadership'],
        ['sec-identity', 'II', 'Identity'],
        ['sec-schedule', 'III', 'Schedule'],
        ['sec-covenant', 'IV', 'Covenant'],
    ];
@endphp

    {{-- ═══ Ink header band ═══ --}}
    <header class="relative grain on-dark bg-ink text-white overflow-hidden"
            style="background-image: radial-gradient(70% 120% at 0% 0%, rgb(165 28 48 / 0.28), transparent 60%), radial-gradient(50% 90% at 100% 100%, rgb(184 137 43 / 0.14), transparent 70%);">
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 pt-6 pb-24 sm:pb-28">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-10 h-10 rounded-full grid place-items-center shrink-0"
                          style="background: radial-gradient(circle at 35% 30%, #C52A40, var(--color-brand-deep)); box-shadow: inset 0 0 0 1px rgb(255 255 255 / .14), 0 0 0 3px rgb(21 18 26), 0 0 0 4px rgb(217 183 108 / .45), 0 8px 18px -6px rgb(165 28 48 / .7);">
                        <x-umc-logo class="w-[1.15rem] h-[1.15rem]" />
                    </span>
                    <span class="leading-tight min-w-0">
                        <span class="block text-[9.5px] font-bold uppercase tracking-[0.24em] text-gold-light/90">Africa University</span>
                        <span class="block font-display text-[16px] text-white truncate" style="font-variation-settings: 'SOFT' 80, 'opsz' 30;">Kwang-Lim Chapel</span>
                    </span>
                </div>
                <a href="{{ route('login') }}" class="btn btn-glass btn-sm shrink-0">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Sign In
                </a>
            </div>

            <div class="mt-12 sm:mt-16">
                <p class="eyebrow eyebrow-gold !text-gold-light">Chaplaincy Office</p>
                <h1 class="page-title !text-white !text-[2.25rem] sm:!text-[3rem] !leading-[1.04]" style="--wght: 400; --soft: 100;">
                    Fellowship Charter Application
                </h1>
                <div class="mt-5 max-w-xl">
                    <p class="text-[14px] font-semibold text-white/90">Welcome to the Fellowship Registry</p>
                    <p class="mt-1.5 text-[13.5px] leading-relaxed text-white/60">
                        Please fill out the details below to register your student fellowship or prayer group. Once submitted, your application will be reviewed by the Chaplaincy Office, and you will receive guidance on meeting spaces, equipment, and charter status.
                    </p>
                </div>

                <ol class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-[12px]" aria-label="Sections of this application">
                    @foreach($sections as [$anchor, $numeral, $label])
                        <li>
                            <a href="#{{ $anchor }}" class="group inline-flex items-baseline gap-2 text-white/55 hover:text-white transition-colors">
                                <span class="font-display text-gold-light/80 group-hover:text-gold-light">{{ $numeral }}</span>
                                <span class="font-medium">{{ $label }}</span>
                            </a>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
        <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gold/50 to-transparent" aria-hidden="true"></div>
    </header>

    {{-- ═══ Paper ═══ --}}
    <main class="relative max-w-3xl mx-auto px-4 sm:px-6 -mt-14 sm:-mt-16 pb-12 space-y-6">

        @if ($errors->any())
            <div class="flash-error" role="alert">
                <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="text-[12.5px]">
                    <p class="font-semibold">Please correct the following fields:</p>
                    <ul class="mt-1 list-disc pl-4 space-y-0.5 opacity-90">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Registration Form Card -->
        <section class="card" aria-label="Charter application form" data-reveal>
            <form method="POST" action="{{ route('clubs.store') }}" enctype="multipart/form-data" class="divide-y divide-slate-100">
                @csrf

                <!-- Section 1: Student Leadership & Contact -->
                <fieldset id="sec-leadership" class="min-w-0 p-5 sm:p-8 space-y-5 scroll-mt-6">
                    <legend class="contents">
                        <span class="flex items-start gap-4">
                            <span class="font-display text-[1.75rem] leading-none text-gold w-9 shrink-0" aria-hidden="true">I</span>
                            <span>
                                <span class="sr-only">1. </span>
                                <span class="block card-title !text-[1.25rem]">Student Leadership &amp; Contact Details</span>
                                <span class="block mt-1 text-[12.5px] text-slate-500">Applicant must be a currently enrolled Africa University student</span>
                            </span>
                        </span>
                    </legend>

                    <div class="sm:pl-13 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="pr_president_name" class="field-label">Founding President Full Name <span class="text-brand">*</span></label>
                            <input type="text" id="pr_president_name" name="founding_president_name" value="{{ old('founding_president_name') }}" required placeholder="e.g. Farai Makoni" autocomplete="name" class="field">
                        </div>
                        <div>
                            <label for="pr_student_id" class="field-label">Student / Staff ID Number <span class="text-brand">*</span></label>
                            <input type="text" id="pr_student_id" name="student_id" value="{{ old('student_id') }}" required placeholder="e.g. AU-2024-8921" class="field font-mono uppercase">
                        </div>
                        <div>
                            <label for="pr_president_email" class="field-label">AU Student Email Address <span class="text-brand">*</span></label>
                            <input type="email" id="pr_president_email" name="founding_president_email" value="{{ old('founding_president_email') }}" required placeholder="e.g. fmakoni@africau.edu" autocomplete="email" class="field">
                        </div>
                        <div>
                            <label for="pr_contact_phone" class="field-label">WhatsApp / Phone Number <span class="text-brand">*</span></label>
                            <input type="text" id="pr_contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" required placeholder="e.g. +263 77 123 4567" autocomplete="tel" class="field">
                        </div>
                        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-dashed border-slate-200">
                            <div>
                                <label for="pr_advisor_name" class="field-label">Faculty / Staff Advisor Name <span class="text-brand">*</span></label>
                                <input type="text" id="pr_advisor_name" name="advisor_name" value="{{ old('advisor_name') }}" required placeholder="e.g. Dr. Tendai Mutasa" class="field">
                            </div>
                            <div>
                                <label for="pr_advisor_email" class="field-label">Faculty Advisor AU Email <span class="text-brand">*</span></label>
                                <input type="email" id="pr_advisor_email" name="advisor_email" value="{{ old('advisor_email') }}" required placeholder="e.g. tmutasa@africau.edu" class="field">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Section 2: Fellowship Identity & Mission -->
                <fieldset id="sec-identity" class="min-w-0 p-5 sm:p-8 space-y-5 scroll-mt-6">
                    <legend class="contents">
                        <span class="flex items-start gap-4">
                            <span class="font-display text-[1.75rem] leading-none text-gold w-9 shrink-0" aria-hidden="true">II</span>
                            <span>
                                <span class="sr-only">2. </span>
                                <span class="block card-title !text-[1.25rem]">Fellowship Identity &amp; Purpose</span>
                                <span class="block mt-1 text-[12.5px] text-slate-500">Name and category under Africa University Chapel</span>
                            </span>
                        </span>
                    </legend>

                    <div class="sm:pl-13 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label for="pr_name" class="field-label">Fellowship Name <span class="text-brand">*</span></label>
                                <input type="text" id="pr_name" name="name" value="{{ old('name') }}" required placeholder="e.g. Anglican Student Society" class="field">
                            </div>
                            <div>
                                <label for="pr_code" class="field-label">Short Code / Acronym <span class="text-brand">*</span></label>
                                <input type="text" id="pr_code" name="code" value="{{ old('code') }}" required placeholder="e.g. ANGLICAN" class="field font-mono uppercase">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="pr_type" class="field-label">Fellowship Category <span class="text-brand">*</span></label>
                                <select id="pr_type" name="type" required class="field">
                                    <option value="">-- Select Category --</option>
                                    <option value="fellowship" {{ old('type') == 'fellowship' ? 'selected' : '' }}>Campus Fellowship / Student Movement</option>
                                    <option value="prayer group" {{ old('type') == 'prayer group' ? 'selected' : '' }}>Intercessory Prayer &amp; Revival Group</option>
                                    <option value="choir or worship team" {{ old('type') == 'choir or worship team' ? 'selected' : '' }}>Choir or Worship Team</option>
                                    <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Community Service &amp; Volunteer Ministry</option>
                                </select>
                            </div>
                            <div>
                                <label for="pr_official_email" class="field-label">Proposed Club Email <span class="text-brand">*</span></label>
                                <input type="email" id="pr_official_email" name="official_email" value="{{ old('official_email') }}" required placeholder="e.g. anglican@africau.edu" class="field">
                            </div>
                        </div>

                        <div>
                            <label for="pr_description" class="field-label">Purpose &amp; Mission Statement <span class="text-brand">*</span></label>
                            <textarea id="pr_description" name="description" rows="4" required placeholder="Describe the mission, activities, and positive impact your fellowship aims to bring to Africa University..." class="field leading-relaxed">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <!-- Section 3: Schedule & Venue -->
                <fieldset id="sec-schedule" class="min-w-0 p-5 sm:p-8 space-y-5 scroll-mt-6">
                    <legend class="contents">
                        <span class="flex items-start gap-4">
                            <span class="font-display text-[1.75rem] leading-none text-gold w-9 shrink-0" aria-hidden="true">III</span>
                            <span>
                                <span class="sr-only">3. </span>
                                <span class="block card-title !text-[1.25rem]">Preferred Schedule &amp; Meeting Space</span>
                                <span class="block mt-1 text-[12.5px] text-slate-500">Regular gathering days and preferred venue</span>
                            </span>
                        </span>
                    </legend>

                    <div class="sm:pl-13 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="pr_meeting_schedule" class="field-label">Preferred Meeting Schedule <span class="text-brand">*</span></label>
                            <input type="text" id="pr_meeting_schedule" name="meeting_schedule" value="{{ old('meeting_schedule') }}" required placeholder="e.g. Fridays 18:00 - 20:00" class="field">
                        </div>
                        <div>
                            <label for="pr_venue" class="field-label">Preferred Campus Venue <span class="text-brand">*</span></label>
                            <select id="pr_venue" name="venue" required class="field">
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

                <!-- Section 4: Constitution & Campus Covenant -->
                <fieldset id="sec-covenant" class="min-w-0 p-5 sm:p-8 space-y-5 scroll-mt-6">
                    <legend class="contents">
                        <span class="flex items-start gap-4">
                            <span class="font-display text-[1.75rem] leading-none text-gold w-9 shrink-0" aria-hidden="true">IV</span>
                            <span>
                                <span class="sr-only">4. </span>
                                <span class="block card-title !text-[1.25rem]">Constitution &amp; Campus Guidelines Covenant</span>
                                <span class="block mt-1 text-[12.5px] text-slate-500">Please attach your group constitution or draft rules</span>
                            </span>
                        </span>
                    </legend>

                    <div class="sm:pl-13 space-y-5">
                        <div>
                            <label for="pr_constitution" class="field-label">Draft Constitution / Guidelines (PDF or Word Document)</label>
                            <input type="file" id="pr_constitution" name="constitution" accept=".pdf,.doc,.docx" aria-describedby="pr_constitution_hint"
                                   class="field !py-2 text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-vellum file:px-3 file:py-1.5 file:text-[12px] file:font-semibold file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer">
                            <p id="pr_constitution_hint" class="field-hint">Your constitution will be kept on file in the Chaplaincy Records Vault.</p>
                        </div>

                        <!-- Campus Covenant Checkbox -->
                        <label class="flex items-start gap-3.5 p-4 sm:p-5 rounded-2xl bg-gold-soft/60 ring-1 ring-gold/25 cursor-pointer">
                            <input type="checkbox" name="covenant_accepted" value="1" required class="mt-1 w-4 h-4 shrink-0 rounded">
                            <span>
                                <span class="block text-[10.5px] font-bold uppercase tracking-[0.16em] text-[#7A5A12]">Campus covenant</span>
                                <span class="block mt-1 font-display italic text-[14.5px] leading-relaxed text-slate-800">
                                    We affirm that our fellowship will abide by the Africa University Student Code of Conduct, Kwang-Lim Chapel policies, and the university's spirit of mutual respect and Christian community.
                                </span>
                            </span>
                        </label>
                    </div>
                </fieldset>

                <!-- Submission Actions -->
                <div class="p-5 sm:px-8 sm:py-6 bg-slate-50/70 rounded-b-[var(--radius-card)] flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-4">
                    <a href="{{ route('login') }}" class="btn btn-ghost self-center sm:self-auto">
                        &larr; Cancel and return to sign in
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto">
                        Submit Fellowship Application
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </div>
            </form>
        </section>

        <!-- Footer -->
        <footer class="pt-4 text-center space-y-3">
            <div class="rule-ornament max-w-[12rem] mx-auto text-[8px]" aria-hidden="true">&#9670;</div>
            <p class="text-[12px] font-medium text-slate-500">Kwang-Lim Chapel &bull; Fellowship Registry</p>
            <p class="font-display italic text-[13px] text-slate-400">School of Dreams in the Valley of Hope</p>
        </footer>

    </main>

</body>
</html>
