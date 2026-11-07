@extends('layouts.app')

@section('title', 'Prepare Service Sheet')

@section('content')
<div class="page-shell max-w-5xl space-y-6">

    <x-page-header eyebrow="Worship Planning · New Service Sheet" title="Prepare Worship Service Sheet"
                   description="Formal order of service and liturgical assignments for university worship.">
        <a href="{{ route('worship.index') }}" class="btn btn-ghost">
            <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12l7.5-7.5M3 12h18"/></svg>
            Worship Planning
        </a>
    </x-page-header>

    <form method="POST" action="{{ route('worship.store') }}" class="card">
        @csrf

        <div class="divide-y divide-slate-100">

            {{-- I. The service --}}
            <section class="grid grid-cols-1 lg:grid-cols-[13rem_minmax(0,1fr)] gap-x-10 gap-y-4 p-6 sm:p-8">
                <div>
                    <p class="font-display text-[13px] text-gold">I.</p>
                    <h2 class="card-title">The service</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">When the congregation gathers.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="ws-service-date" class="field-label">Service Date <span class="text-brand">*</span></label>
                        <input type="date" id="ws-service-date" name="service_date" value="{{ old('service_date', date('Y-m-d', strtotime('next Sunday'))) }}" required class="field">
                    </div>
                    <div>
                        <label for="ws-service-type" class="field-label">Service Type <span class="text-brand">*</span></label>
                        <select id="ws-service-type" name="service_type" required class="field">
                            <option value="Sunday 9:00 a.m.">Sunday 9:00 a.m. University Worship</option>
                            <option value="Wednesday 8:00 a.m.">Wednesday 8:00 a.m. Midweek Chapel</option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- II. Word & theme --}}
            <section class="grid grid-cols-1 lg:grid-cols-[13rem_minmax(0,1fr)] gap-x-10 gap-y-4 p-6 sm:p-8">
                <div>
                    <p class="font-display text-[13px] text-gold">II.</p>
                    <h2 class="card-title">The Word</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Who preaches and leads, and the text they open.</p>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="ws-preacher" class="field-label">Preacher</label>
                            <input type="text" id="ws-preacher" name="preacher" placeholder="e.g. Rev. Supt. Eunice Marima" class="field">
                        </div>
                        <div>
                            <label for="ws-liturgist" class="field-label">Liturgist</label>
                            <input type="text" id="ws-liturgist" name="liturgist" placeholder="e.g. Pastor Micah David" class="field">
                        </div>
                    </div>
                    <div>
                        <label for="ws-theme" class="field-label">Theme / Sermon Title</label>
                        <input type="text" id="ws-theme" name="theme" placeholder="e.g. Anchored in Hope, Transforming Africa" class="field font-display !text-[15px]">
                    </div>
                    <div>
                        <label for="ws-scripture" class="field-label">Scripture Reading(s)</label>
                        <input type="text" id="ws-scripture" name="scripture" placeholder="e.g. Isaiah 40:28-31; Romans 12:1-2" class="field">
                    </div>
                </div>
            </section>

            {{-- III. Hymnody --}}
            <section class="grid grid-cols-1 lg:grid-cols-[13rem_minmax(0,1fr)] gap-x-10 gap-y-4 p-6 sm:p-8">
                <div>
                    <p class="font-display text-[13px] text-gold">III.</p>
                    <h2 class="card-title">Hymnody</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Look a hymn up by number, then insert its title and stanzas below.</p>
                </div>
                <div class="space-y-4 min-w-0">
                    <!-- The United Methodist Hymnal (UMH) Interactive Lookup -->
                    @include('worship.partials.hymn_picker')

                    <div>
                        <label for="ws-hymns" class="field-label">Hymns (Separate with semicolons)</label>
                        <textarea id="ws-hymns" name="hymns" rows="2" placeholder="e.g. UMH 57: O For a Thousand Tongues; UMH 368: My Hope Is Built; Shona Hymn 11" class="field">{{ old('hymns') }}</textarea>
                    </div>

                    <div>
                        <div class="flex flex-wrap items-baseline justify-between gap-x-3">
                            <label for="ws-lyrics" class="field-label">Hymn & Praise Lyrics</label>
                            <span class="text-[12px] text-slate-500 mb-1.5">Used for media projection slides & bulletin</span>
                        </div>
                        <textarea id="ws-lyrics" name="lyrics" rows="7" placeholder="Enter or paste hymn stanzas, praise chorus, and worship lyrics here...&#10;e.g.&#10;[Hymn: My Hope Is Built]&#10;Verse 1: My hope is built on nothing less than Jesus' blood and righteousness...&#10;&#10;[Praise Chorus]&#10;Refrain: Lord You reign forever and ever..." class="field font-display !text-[14px] !leading-[1.75]">{{ old('lyrics') }}</textarea>
                        <p class="field-hint">Leave a blank line between stanzas; each stanza becomes its own projection slide.</p>
                    </div>
                </div>
            </section>

            {{-- IV. Ministers --}}
            <section class="grid grid-cols-1 lg:grid-cols-[13rem_minmax(0,1fr)] gap-x-10 gap-y-4 p-6 sm:p-8">
                <div>
                    <p class="font-display text-[13px] text-gold">IV.</p>
                    <h2 class="card-title">Ministers of worship</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Readers, singers, ushers and the media desk.</p>
                </div>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="ws-bible-reader" class="field-label">Bible Reader</label>
                            <input type="text" id="ws-bible-reader" name="bible_reader" placeholder="e.g. Student President" class="field">
                        </div>
                        <div>
                            <label for="ws-choir" class="field-label">Choir</label>
                            <input type="text" id="ws-choir" name="choir" placeholder="e.g. Praise the Lord Choir" class="field">
                        </div>
                        <div>
                            <label for="ws-praise-team" class="field-label">Praise & Worship Team</label>
                            <input type="text" id="ws-praise-team" name="praise_worship_team" placeholder="e.g. Band A" class="field">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="ws-ushers" class="field-label">Ushers</label>
                            <input type="text" id="ws-ushers" name="ushers" placeholder="e.g. Fairfield Buddies Squad" class="field">
                        </div>
                        <div>
                            <label for="ws-media-contact" class="field-label">Media Contact</label>
                            <input type="text" id="ws-media-contact" name="media_contact" placeholder="e.g. media@africau.edu" class="field">
                        </div>
                    </div>

                    <!-- Accessibility & Sign-Language Interpreter -->
                    <div class="rounded-xl bg-paper/70 ring-1 ring-slate-200/70 p-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" id="sign_interpreter_required" name="sign_interpreter_required" value="1" onchange="document.getElementById('sign_name_box').classList.toggle('hidden', !this.checked)" class="mt-0.5 w-4 h-4 rounded">
                            <label for="sign_interpreter_required" class="text-[13px] font-semibold text-slate-800 leading-snug">Assign Sign-Language Interpreter (Accessibility & Special Needs Ministry)</label>
                        </div>
                        <div id="sign_name_box" class="hidden pl-7">
                            <label for="ws-sign-name" class="field-label">Interpreter Name / Unit</label>
                            <input type="text" id="ws-sign-name" name="sign_interpreter_name" placeholder="e.g. Sister Chipo / AU Special Needs Guild" class="field">
                        </div>
                    </div>
                </div>
            </section>

            {{-- V. Notes & status --}}
            <section class="grid grid-cols-1 lg:grid-cols-[13rem_minmax(0,1fr)] gap-x-10 gap-y-4 p-6 sm:p-8">
                <div>
                    <p class="font-display text-[13px] text-gold">V.</p>
                    <h2 class="card-title">Call &amp; readiness</h2>
                    <p class="mt-1 text-[12px] text-slate-500 leading-relaxed">Special litany or observances, and where the sheet stands.</p>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="ws-notes" class="field-label">Call to Services</label>
                        <textarea id="ws-notes" name="order_of_service_notes" rows="3" placeholder="Special litany, call to worship, holy communion, or robes..." class="field">{{ old('order_of_service_notes') }}</textarea>
                    </div>
                    <div class="sm:max-w-xs">
                        <label for="ws-status" class="field-label">Status</label>
                        <select id="ws-status" name="status" class="field">
                            <option value="draft">Draft (In Preparation)</option>
                            <option value="ready">Ready (Approved for Sunday/Wednesday)</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </section>
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 px-6 py-4 sm:px-8 border-t border-slate-100 bg-slate-50/60 rounded-b-[var(--radius-card)]">
            <a href="{{ route('worship.index') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Service Sheet</button>
        </div>
    </form>

</div>
@endsection
