@extends('layouts.app')

@section('title', $report->title)

@section('content')
<div class="page-shell max-w-4xl space-y-6">

    <x-page-header eyebrow="Post-activity report" :title="$report->title">
        <span class="badge badge-dot capitalize {{ $report->status === 'reviewed' ? 'badge-emerald' : 'badge-sky' }}">{{ $report->status }}</span>
    </x-page-header>

    {{-- Provenance --}}
    <div class="-mt-4 flex flex-wrap items-center gap-x-3 gap-y-1 text-[12px] text-slate-500">
        <nav aria-label="Breadcrumb" class="flex items-center gap-2">
            <a href="{{ route('reports.index') }}" class="font-semibold text-brand hover:text-brand-dark">Reports</a>
            <span class="text-slate-300" aria-hidden="true">/</span>
            <span class="text-slate-700 tabular-nums" aria-current="page">Report #{{ $report->id }}</span>
        </nav>
        <span class="text-slate-300" aria-hidden="true">·</span>
        <span>Submitted by {{ $report->submitter?->name }} ({{ $report->club?->name }}) on {{ $report->created_at->format('M d, Y') }}</span>
    </div>

    {{-- Summary --}}
    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="dash-stat-card" style="--tone: var(--color-brand)">
            <dt class="text-[10.5px] font-bold uppercase tracking-[0.14em] text-slate-500">Actual Attendance</dt>
            <dd class="mt-4 flex items-baseline gap-2">
                <span class="stat-value text-[2.4rem] text-slate-900">{{ $report->attendance }}</span>
                <span class="text-[12px] text-slate-500">attendees</span>
            </dd>
        </div>
        <div class="dash-stat-card" style="--tone: var(--color-gold)">
            <dt class="text-[10.5px] font-bold uppercase tracking-[0.14em] text-slate-500">Total Expenditure</dt>
            <dd class="mt-4 flex items-baseline gap-0.5 text-slate-900">
                <span class="font-display text-[1.4rem] text-slate-400">$</span><span class="stat-value text-[2.4rem]">{{ number_format($report->spending, 2) }}</span>
            </dd>
        </div>
        <div class="dash-stat-card col-span-2 sm:col-span-1" style="--tone: var(--color-slate-300)">
            <dt class="text-[10.5px] font-bold uppercase tracking-[0.14em] text-slate-500">Sponsoring Club</dt>
            <dd class="mt-4 font-display text-[1.25rem] leading-snug text-slate-900">{{ $report->club?->name }}</dd>
        </div>
    </dl>

    {{-- Narrative --}}
    <section class="card">
        <div class="card-header">
            <div>
                <p class="card-kicker">In their own words</p>
                <h2 class="card-title mt-0.5">Outcomes &amp; spiritual impact narrative</h2>
            </div>
        </div>
        <div class="card-body">
            <p class="font-display text-[15px] leading-[1.8] text-slate-800 whitespace-pre-line max-w-prose" style="font-variation-settings: 'SOFT' 50, 'opsz' 14;">{{ $report->outcome }}</p>
        </div>
    </section>

    {{-- Photos --}}
    @if(!empty($report->photos) && is_array($report->photos))
        <section class="card">
            <div class="card-header">
                <h2 class="card-title">Activity photo gallery</h2>
                <span class="text-[12px] text-slate-400 tabular-nums">{{ count($report->photos) }}</span>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($report->photos as $photoIndex => $photo)
                        <a href="{{ route('reports.photo', [$report->id, $photoIndex]) }}" target="_blank" rel="noopener" class="block rounded-xl overflow-hidden ring-1 ring-slate-200 aspect-video bg-slate-100 transition hover:ring-gold-light hover:shadow-md">
                            <img src="{{ route('reports.photo', [$report->id, $photoIndex]) }}" alt="Event photo {{ $photoIndex + 1 }}" loading="lazy" class="w-full h-full object-cover">
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Chaplaincy remarks --}}
    <section class="card">
        <div class="card-header">
            <div>
                <p class="card-kicker">Office of the Chaplain</p>
                <h2 class="card-title mt-0.5">Chaplaincy office review remarks</h2>
            </div>
        </div>
        <div class="card-body space-y-5">
            @if($report->chaplaincy_remarks)
                <blockquote class="relative pl-5 border-l-2 border-gold">
                    <p class="font-display italic text-[15px] leading-relaxed text-slate-800 whitespace-pre-line">{{ $report->chaplaincy_remarks }}</p>
                </blockquote>
            @else
                <p class="text-[13px] text-slate-500 italic">No formal review remarks recorded yet.</p>
            @endif

            @if(auth()->user()->isOffice())
                <form method="POST" action="{{ route('reports.remarks', $report->id) }}" class="space-y-3 pt-5 border-t border-slate-100">
                    @csrf
                    <label for="chaplaincy_remarks_field" class="field-label">Commendation or follow-up note</label>
                    <textarea id="chaplaincy_remarks_field" name="chaplaincy_remarks" rows="3" required placeholder="Add chaplaincy commendation or follow-up note..." class="field leading-relaxed">{{ $report->chaplaincy_remarks }}</textarea>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Save Chaplaincy Remarks</button>
                    </div>
                </form>
            @endif
        </div>
    </section>

</div>
@endsection
