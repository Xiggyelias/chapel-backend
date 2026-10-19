@extends('layouts.app')

@section('title', 'Chapel Guidelines & Operating Standards')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 animate-fade-in">

    <!-- Header Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#171717] via-[#220B11] to-[#DC2626] p-8 text-white shadow-2xl border-t-2 border-[#DC2626]">
        <div class="absolute -right-12 -bottom-16 w-80 h-80 rounded-full bg-[#DC2626]/20 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <div class="flex items-center space-x-2 text-xs font-bold tracking-widest text-rose-200 uppercase mb-2">
                    <span>Africa University Chaplaincy</span>
                    <span>&bull;</span>
                    <span>Institutional Governance</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black font-cinzel tracking-tight text-white">
                    Chapel Guidelines & Operating Standards
                </h1>
                <p class="text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
                    Official protocols, approval criteria, venue regulations, and spiritual conduct standards governing all 10 registered fellowships and ministry clubs at Kwang-Lim Chapel.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="window.print()" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold backdrop-blur-md transition border border-white/20 shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print Policy Handbook</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Index Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="#fourteen-day-rule" class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#DC2626] transition group">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center font-bold text-sm mb-3 group-hover:scale-105 transition">14d</div>
            <h3 class="text-xs font-bold text-slate-900 group-hover:text-[#DC2626] transition">14-Day Advance Notice</h3>
            <p class="text-[11px] text-slate-500 mt-1">Submission timelines & exception protocols</p>
        </a>
        <a href="#vc-clearance" class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#DC2626] transition group">
            <div class="w-10 h-10 rounded-xl bg-red-50 text-[#DC2626] flex items-center justify-center font-bold text-sm mb-3 group-hover:scale-105 transition">VC</div>
            <h3 class="text-xs font-bold text-slate-900 group-hover:text-[#DC2626] transition">VC Clearance Protocol</h3>
            <p class="text-[11px] text-slate-500 mt-1">Outings & high-profile campus events</p>
        </a>
        <a href="#venue-rules" class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#DC2626] transition group">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-sm mb-3 group-hover:scale-105 transition">🏛️</div>
            <h3 class="text-xs font-bold text-slate-900 group-hover:text-[#DC2626] transition">Sanctuary & Venue Rules</h3>
            <p class="text-[11px] text-slate-500 mt-1">Capacities, sound equipment, curfew</p>
        </a>
        <a href="#reporting-712" class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-xs hover:border-[#DC2626] transition group">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-sm mb-3 group-hover:scale-105 transition">§7</div>
            <h3 class="text-xs font-bold text-slate-900 group-hover:text-[#DC2626] transition">Chapel Manual §7.1.2</h3>
            <p class="text-[11px] text-slate-500 mt-1">Post-activity report & spending audits</p>
        </a>
    </div>

    <!-- Policy Sections -->
    <div class="space-y-6">

        <!-- Section 1: The 14-Day Advance Notice Rule -->
        <div id="fourteen-day-rule" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                <span class="w-2.5 h-8 rounded-full bg-[#DC2626]"></span>
                <div>
                    <h2 class="text-lg font-bold font-cinzel text-slate-900">Section 1: The Mandatory 14-Day Advance Notice Rule</h2>
                    <p class="text-xs text-slate-500">Chapel Constitution & Operating Procedures §4.2</p>
                </div>
            </div>
            <div class="prose prose-sm text-slate-600 max-w-none text-xs sm:text-sm leading-relaxed space-y-3">
                <p>
                    All chapel fellowship and ministry proposals involving venue reservations, public campus promotions, sound equipment requisition, or off-campus travel must be formally submitted through this portal <strong>at least fourteen (14) calendar days prior</strong> to the planned execution date.
                </p>
                <div class="p-4 rounded-2xl bg-red-50/60 border border-red-200/80 text-xs text-red-900 space-y-2">
                    <div class="font-bold flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Automated System Enforcement:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Requests submitted fewer than 14 days in advance are automatically flagged with an expedited warning banner in the Chaplain's console.</li>
                        <li>Late submissions require recorded justification and written administrative concurrence before approval can be granted.</li>
                        <li>Club leaders who consistently submit late requests may have room booking privileges temporarily suspended for that semester.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section 2: Vice-Chancellor Concurrence Protocol -->
        <div id="vc-clearance" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                <span class="w-2.5 h-8 rounded-full bg-[#DC2626]"></span>
                <div>
                    <h2 class="text-lg font-bold font-cinzel text-slate-900">Section 2: Vice-Chancellor Clearance & Off-Campus Outings</h2>
                    <p class="text-xs text-slate-500">University Executive Governance Directive §2.1</p>
                </div>
            </div>
            <div class="prose prose-sm text-slate-600 max-w-none text-xs sm:text-sm leading-relaxed space-y-3">
                <p>
                    In accordance with Africa University executive protocols, specific categories of student ministry events require prior executive concurrence from the Vice-Chancellor before final Chaplaincy authorization:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <span class="text-xs font-bold text-slate-900 block">Events Requiring VC Go-Ahead:</span>
                        <ul class="list-disc pl-4 text-xs text-slate-600 space-y-1">
                            <li>All off-campus travel, mission trips, and choir tours.</li>
                            <li>Events expecting attendance of more than 500 congregants.</li>
                            <li>Conferences featuring external guest preachers or international ministers.</li>
                            <li>All-night prayer revivals extending past 23:00 curfew.</li>
                        </ul>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <span class="text-xs font-bold text-slate-900 block">Approval Procedure:</span>
                        <p class="text-xs text-slate-600">
                            The Chaplain or Assistant Chaplain must attach the written Vice-Chancellor concurrence memo or record the formal clearance reference number directly into the proposal dossier before the system permits final status approval.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: Venue Stewardship & Curfew -->
        <div id="venue-rules" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                <span class="w-2.5 h-8 rounded-full bg-slate-800"></span>
                <div>
                    <h2 class="text-lg font-bold font-cinzel text-slate-900">Section 3: Kwang-Lim Chapel Sanctuary & Venue Stewardship</h2>
                    <p class="text-xs text-slate-500">Sanctuary Sacred Spaces & Asset Protection</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <span class="font-bold text-slate-900 block">Main Sanctuary (Cap: 1,200)</span>
                    <p class="text-slate-600">Reserved for university-wide worship, joint revivals, and designated Sunday/Wednesday assemblies. No food or sweetened beverages allowed.</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <span class="font-bold text-slate-900 block">Chapel Lower Hall (Cap: 250)</span>
                    <p class="text-slate-600">Multi-purpose facility for choir rehearsals, fellowships, and committee forums. Must be left broom-clean after every session.</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <span class="font-bold text-slate-900 block">Peace & Prayer Gardens</span>
                    <p class="text-slate-600">Contemplative outdoor venues for small prayer cells and acoustic worship. Strict noise controls apply after 20:00.</p>
                </div>
            </div>
        </div>

        <!-- Section 4: Post-Activity Reporting (Manual 7.1.2) -->
        <div id="reporting-712" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                <span class="w-2.5 h-8 rounded-full bg-emerald-600"></span>
                <div>
                    <h2 class="text-lg font-bold font-cinzel text-slate-900">Section 4: Post-Activity Reporting & Financial Reconciliation</h2>
                    <p class="text-xs text-slate-500">Chapel Manual §7.1.2 Mandatory Accountability</p>
                </div>
            </div>
            <div class="prose prose-sm text-slate-600 max-w-none text-xs sm:text-sm leading-relaxed space-y-3">
                <p>
                    Within <strong>seven (7) days</strong> following the conclusion of any approved activity, the club executive must submit an official Post-Activity Report through this portal capturing:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-900 font-semibold">1. Verified Headcount</div>
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-900 font-semibold">2. Spiritual Outcomes</div>
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-900 font-semibold">3. Actual Expenditure</div>
                    <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-900 font-semibold">4. Archival Photos</div>
                </div>
                <p class="text-xs text-slate-500 italic">
                    Subsequent activity proposals are automatically locked until outstanding reports under §7.1.2 have been filed and reviewed by the Chaplaincy Office.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
