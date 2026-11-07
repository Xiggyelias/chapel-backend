@extends('layouts.app')

@section('title', 'Leadership Handover')

@section('content')
@php
    $protocol = [
        ['Office Initiates', 'Secretary registers incoming leader and starts process.'],
        ['Outgoing Checklist', 'Outgoing president files running matters & traditions.'],
        ['Vault Verification', 'Chaplaincy verifies vault repository completeness.'],
        ['Joint Meeting', 'Formal pastoral counsel meeting with Chaplaincy.'],
        ['Access Transfer', 'Administrator transfers official email & permissions.'],
    ];
@endphp
<div class="page-shell space-y-6">

    <x-page-header eyebrow="Leadership transition" title="Leadership Handover Management"
                   description="Structured 5-step transition protocol ensuring permanent club archives, handover checklists, and access transfers">
        @if(auth()->user()->isOffice())
            <button type="button" onclick="document.getElementById('newHandoverModal').classList.toggle('hidden')" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"/></svg>
                Initiate Club Handover
            </button>
        @endif
    </x-page-header>

    {{-- The protocol --}}
    <section class="card" aria-labelledby="protocolTitle">
        <div class="card-header">
            <div>
                <p class="card-kicker">The protocol</p>
                <h2 id="protocolTitle" class="card-title mt-0.5">Five steps of a faithful handover</h2>
            </div>
        </div>
        <ol class="grid grid-cols-1 sm:grid-cols-5">
            @foreach($protocol as $i => [$name, $text])
                <li class="relative p-5 {{ !$loop->last ? 'border-b sm:border-b-0 sm:border-r border-slate-100' : '' }}">
                    <span class="font-display text-[1.75rem] leading-none text-gold tabular-nums">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="sr-only">Step {{ $i + 1 }}</span>
                    <h3 class="mt-3 text-[13.5px] font-semibold text-slate-900">{{ $name }}</h3>
                    <p class="mt-1 text-[12px] leading-relaxed text-slate-500">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Handovers --}}
    <section class="card overflow-hidden">
        <div class="card-header">
            <h2 class="card-title">Active &amp; historical transitions</h2>
            <span class="text-[12px] text-slate-500"><span class="font-semibold text-slate-800 tabular-nums">{{ $handovers->total() }}</span> Records</span>
        </div>

        @forelse($handovers as $h)
            <article class="group px-5 py-5 sm:px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:bg-slate-50/70 {{ !$loop->last ? 'border-b border-slate-100' : '' }}">
                <div class="min-w-0 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 text-[12px] font-semibold text-slate-700">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $h->club?->color_hex ?? '#DC2626' }}" aria-hidden="true"></span>
                            {{ $h->club?->name }} ({{ $h->club?->code }})
                        </span>
                        <span class="text-slate-300" aria-hidden="true">/</span>
                        <span class="text-[12px] text-slate-500 tabular-nums">{{ $h->academic_year }}</span>
                        <span class="badge badge-dot capitalize {{ $h->status === 'completed' ? 'badge-emerald' : 'badge-amber' }}">{{ str_replace('_', ' ', $h->status) }}</span>
                    </div>

                    <h3 class="font-display text-[1.2rem] leading-snug text-slate-900">
                        {{ $h->outgoing_president_name }} <span class="text-gold mx-1" aria-label="to">&rarr;</span> {{ $h->incoming_president_name }}
                    </h3>

                    <div class="text-[12px] text-slate-500 flex flex-wrap items-center gap-x-5 gap-y-1">
                        <span>Official Email: <span class="font-mono text-slate-700">{{ $h->club?->official_email }}</span></span>
                        <span>Incoming Contact: <span class="font-mono text-slate-700">{{ $h->incoming_president_email }}</span></span>
                    </div>
                </div>

                <a href="{{ route('handover.show', $h->id) }}" class="btn btn-secondary shrink-0 self-start sm:self-auto">
                    Review Progress
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5l7 7-7 7"/></svg>
                </a>
            </article>
        @empty
            <div class="empty-state">
                <div class="empty-state-mark" aria-hidden="true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <p class="empty-state-title">No Active Handovers</p>
                <p class="empty-state-text">When executive tenure concludes, the Chaplaincy initiates a formal 5-step transition.</p>
                @if(auth()->user()->isOffice())
                    <button type="button" onclick="document.getElementById('newHandoverModal').classList.toggle('hidden')" class="btn btn-primary btn-sm mt-4">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4"/></svg>
                        Initiate Club Handover
                    </button>
                @endif
            </div>
        @endforelse
    </section>

    @if($handovers->hasPages())
        <div>{{ $handovers->links() }}</div>
    @endif

</div>

{{-- Initiate handover --}}
<div id="newHandoverModal" role="dialog" aria-modal="true" aria-labelledby="newHandoverTitle" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-ink/50 backdrop-blur-sm p-4 modal-backdrop">
    <div class="bg-white rounded-3xl shadow-lg max-w-md w-full ring-1 ring-black/5 overflow-hidden modal-panel max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="px-6 pt-6 pb-4 flex items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Chapel governance</p>
                <h3 id="newHandoverTitle" class="font-display text-xl text-slate-900">Initiate club handover</h3>
            </div>
            <button type="button" onclick="document.getElementById('newHandoverModal').classList.add('hidden')" class="topbar-action -mr-2 -mt-1" aria-label="Close">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('handover.store') }}" class="px-6 pb-6 space-y-4">
            @csrf

            <div>
                <label for="handover_club_id" class="field-label">Select club <span class="text-brand">*</span></label>
                <select id="handover_club_id" name="club_id" required class="field">
                    @foreach($clubs as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="handover_academic_year" class="field-label">Academic year <span class="text-brand">*</span></label>
                <input type="text" id="handover_academic_year" name="academic_year" value="2026/2027" required class="field tabular-nums">
            </div>

            <div>
                <label for="handover_incoming_name" class="field-label">Incoming president full name <span class="text-brand">*</span></label>
                <input type="text" id="handover_incoming_name" name="incoming_president_name" required placeholder="e.g. Farai Makoni" class="field">
            </div>

            <div>
                <label for="handover_incoming_email" class="field-label">Incoming president official/student email <span class="text-brand">*</span></label>
                <input type="email" id="handover_incoming_email" name="incoming_president_email" required placeholder="e.g. fmakoni@africau.edu" class="field">
            </div>

            <div>
                <label for="handover_incoming_phone" class="field-label">Incoming president phone</label>
                <input type="text" id="handover_incoming_phone" name="incoming_president_phone" placeholder="+263 77 ..." class="field">
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('newHandoverModal').classList.add('hidden')" class="btn btn-ghost">Cancel</button>
                <button type="submit" class="btn btn-primary">Start Handover</button>
            </div>
        </form>
    </div>
</div>
@endsection
