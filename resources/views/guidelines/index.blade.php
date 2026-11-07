@extends('layouts.app')

@section('title', 'Chapel Guidelines & Operating Standards')

@section('content')
<div class="page-shell space-y-8">

    <x-page-header eyebrow="Africa University Chaplaincy · Institutional Governance" title="Chapel Guidelines & Operating Standards"
                   description="Official protocols, approval criteria, venue regulations, and spiritual conduct standards governing all 10 registered fellowships and ministry clubs at Kwang-Lim Chapel.">
        <button type="button" onclick="window.print()" class="btn btn-secondary no-print">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Policy Handbook
        </button>
    </x-page-header>

    {{-- Quick index --}}
    <nav class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" aria-label="Policy sections">
        @foreach([
            ['#fourteen-day-rule', '14d', '14-Day Advance Notice', 'Submission timelines & exception protocols', 'I'],
            ['#vc-clearance', 'VC', 'VC Clearance Protocol', 'Outings & high-profile campus events', 'II'],
            ['#venue-rules', null, 'Sanctuary & Venue Rules', 'Capacities, sound equipment, curfew', 'III'],
            ['#reporting-712', '§7', 'Chapel Manual §7.1.2', 'Post-activity report & spending audits', 'IV'],
        ] as [$href, $mark, $label, $hint, $numeral])
            <a href="{{ $href }}" class="card group block p-5 hover:border-slate-300 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <span class="w-11 h-11 rounded-full grid place-items-center font-display text-[15px] text-gold"
                          style="background: radial-gradient(circle at 50% 35%, #fff, var(--color-gold-soft)); box-shadow: inset 0 0 0 1px rgb(184 137 43 / .28);">
                        @if($mark)
                            {{ $mark }}
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 21h18M4 21V10l8-6 8 6v11M9 21v-6a3 3 0 016 0v6M12 4V2"/></svg>
                        @endif
                    </span>
                    <span class="font-display italic text-[13px] text-slate-300" aria-hidden="true">{{ $numeral }}</span>
                </div>
                <h3 class="mt-4 text-[13.5px] font-semibold text-slate-900 group-hover:text-brand transition-colors">{{ $label }}</h3>
                <p class="text-[12px] text-slate-500 mt-1 leading-relaxed">{{ $hint }}</p>
            </a>
        @endforeach
    </nav>

    {{-- Policy sections --}}
    <div class="space-y-6">

        {{-- Section 1: The 14-Day Advance Notice Rule --}}
        <section id="fourteen-day-rule" class="card scroll-mt-24">
            <div class="card-header !items-start !px-6 sm:!px-8 !pt-6 !pb-5">
                <div>
                    <p class="card-kicker">Chapel Constitution &amp; Operating Procedures §4.2</p>
                    <h2 class="card-title !text-[1.35rem] mt-1 leading-snug"><span class="text-gold">Section 1:</span> The Mandatory 14-Day Advance Notice Rule</h2>
                </div>
            </div>
            <div class="px-6 sm:px-8 py-6 space-y-4 text-[13.5px] text-slate-600 leading-relaxed max-w-4xl">
                <p>
                    All chapel fellowship and ministry proposals involving venue reservations, public campus promotions, sound equipment requisition, or off-campus travel must be formally submitted through this portal <strong class="font-semibold text-slate-900">at least fourteen (14) calendar days prior</strong> to the planned execution date.
                </p>
                <div class="relative p-5 pl-6 rounded-2xl bg-brand-soft/70 ring-1 ring-brand/15 text-[13px] text-brand-deep space-y-2.5 overflow-hidden">
                    <span class="absolute left-0 top-0 bottom-0 w-[3px] bg-brand" aria-hidden="true"></span>
                    <div class="font-semibold flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Automated System Enforcement:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1.5 marker:text-brand/60">
                        <li>Requests submitted fewer than 14 days in advance are automatically flagged with an expedited warning banner in the Chaplain's console.</li>
                        <li>Late submissions require recorded justification and written administrative concurrence before approval can be granted.</li>
                        <li>Club leaders who consistently submit late requests may have room booking privileges temporarily suspended for that semester.</li>
                    </ul>
                </div>
            </div>
        </section>

        {{-- Section 2: Vice-Chancellor Concurrence Protocol --}}
        <section id="vc-clearance" class="card scroll-mt-24">
            <div class="card-header !items-start !px-6 sm:!px-8 !pt-6 !pb-5">
                <div>
                    <p class="card-kicker">University Executive Governance Directive §2.1</p>
                    <h2 class="card-title !text-[1.35rem] mt-1 leading-snug"><span class="text-gold">Section 2:</span> Vice-Chancellor Clearance &amp; Off-Campus Outings</h2>
                </div>
            </div>
            <div class="px-6 sm:px-8 py-6 space-y-4 text-[13.5px] text-slate-600 leading-relaxed">
                <p class="max-w-4xl">
                    In accordance with Africa University executive protocols, specific categories of student ministry events require prior executive concurrence from the Vice-Chancellor before final Chaplaincy authorization:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-5 rounded-2xl bg-slate-50 ring-1 ring-slate-200/70 space-y-2.5">
                        <span class="text-[13px] font-semibold text-slate-900 block">Events Requiring VC Go-Ahead:</span>
                        <ul class="list-disc pl-4 text-[13px] text-slate-600 space-y-1.5 marker:text-gold">
                            <li>All off-campus travel, mission trips, and choir tours.</li>
                            <li>Events expecting attendance of more than 500 congregants.</li>
                            <li>Conferences featuring external guest preachers or international ministers.</li>
                            <li>All-night prayer revivals extending past 23:00 curfew.</li>
                        </ul>
                    </div>
                    <div class="p-5 rounded-2xl bg-slate-50 ring-1 ring-slate-200/70 space-y-2.5">
                        <span class="text-[13px] font-semibold text-slate-900 block">Approval Procedure:</span>
                        <p class="text-[13px] text-slate-600">
                            The Chaplain or Assistant Chaplain must attach the written Vice-Chancellor concurrence memo or record the formal clearance reference number directly into the proposal dossier before the system permits final status approval.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section 3: Venue Stewardship & Curfew --}}
        <section id="venue-rules" class="card scroll-mt-24">
            <div class="card-header !items-start !px-6 sm:!px-8 !pt-6 !pb-5">
                <div>
                    <p class="card-kicker">Sanctuary Sacred Spaces &amp; Asset Protection</p>
                    <h2 class="card-title !text-[1.35rem] mt-1 leading-snug"><span class="text-gold">Section 3:</span> Kwang-Lim Chapel Sanctuary &amp; Venue Stewardship</h2>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 md:divide-x divide-y md:divide-y-0 divide-slate-100 text-[13px]">
                <div class="px-6 sm:px-8 py-6 space-y-1.5">
                    <span class="font-display text-[1.05rem] text-slate-900 block">Main Sanctuary (Cap: 1,200)</span>
                    <p class="text-slate-600 leading-relaxed">Reserved for university-wide worship, joint revivals, and designated Sunday/Wednesday assemblies. No food or sweetened beverages allowed.</p>
                </div>
                <div class="px-6 sm:px-8 py-6 space-y-1.5">
                    <span class="font-display text-[1.05rem] text-slate-900 block">Chapel Lower Hall (Cap: 250)</span>
                    <p class="text-slate-600 leading-relaxed">Multi-purpose facility for choir rehearsals, fellowships, and committee forums. Must be left broom-clean after every session.</p>
                </div>
                <div class="px-6 sm:px-8 py-6 space-y-1.5">
                    <span class="font-display text-[1.05rem] text-slate-900 block">Peace &amp; Prayer Gardens</span>
                    <p class="text-slate-600 leading-relaxed">Contemplative outdoor venues for small prayer cells and acoustic worship. Strict noise controls apply after 20:00.</p>
                </div>
            </div>
        </section>

        {{-- Section 4: Post-Activity Reporting (Manual 7.1.2) --}}
        <section id="reporting-712" class="card scroll-mt-24">
            <div class="card-header !items-start !px-6 sm:!px-8 !pt-6 !pb-5">
                <div>
                    <p class="card-kicker">Chapel Manual §7.1.2 Mandatory Accountability</p>
                    <h2 class="card-title !text-[1.35rem] mt-1 leading-snug"><span class="text-gold">Section 4:</span> Post-Activity Reporting &amp; Financial Reconciliation</h2>
                </div>
            </div>
            <div class="px-6 sm:px-8 py-6 space-y-5 text-[13.5px] text-slate-600 leading-relaxed">
                <p class="max-w-4xl">
                    Within <strong class="font-semibold text-slate-900">seven (7) days</strong> following the conclusion of any approved activity, the club executive must submit an official Post-Activity Report through this portal capturing:
                </p>
                <ol class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-[13px]">
                    @foreach(['Verified Headcount', 'Spiritual Outcomes', 'Actual Expenditure', 'Archival Photos'] as $i => $capture)
                        <li class="p-4 rounded-2xl bg-slate-50 ring-1 ring-slate-200/70">
                            <span class="block font-display text-[1.6rem] leading-none text-gold tabular-nums">{{ $i + 1 }}.</span>
                            <span class="block mt-2 font-semibold text-slate-900">{{ $capture }}</span>
                        </li>
                    @endforeach
                </ol>
                <p class="text-[12.5px] text-slate-500 italic">
                    Subsequent activity proposals are automatically locked until outstanding reports under §7.1.2 have been filed and reviewed by the Chaplaincy Office.
                </p>
            </div>
        </section>

    </div>

</div>
@endsection
