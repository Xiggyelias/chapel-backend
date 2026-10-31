@extends('layouts.app')

@section('title', 'Leadership Handover')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs font-semibold text-[#DC2626] tracking-wider uppercase">
                <span>Kwang-Lim Chapel</span>
                <span>&bull;</span>
                <span class="text-slate-400 font-medium">Leadership Transition</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                Leadership Handover Management
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Structured 5-step transition protocol ensuring permanent club archives, handover checklists, and access transfers
            </p>
        </div>

        @if(auth()->user()->isOffice())
            <button type="button" onclick="document.getElementById('newHandoverModal').classList.toggle('hidden')" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-bold uppercase tracking-wider transition shadow-sm self-start sm:self-auto active:scale-95 cursor-pointer">
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Initiate Club Handover</span>
            </button>
        @endif
    </div>

    <!-- Handover 5-Step Process Explainer -->
    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-xs">
        <div class="bg-white p-4 rounded-2xl card-elevation border border-slate-200/80 space-y-1 relative overflow-hidden">
            <span class="text-[10px] font-bold text-[#DC2626] bg-[#DC2626]/10 px-2 py-0.5 rounded-full border border-[#DC2626]/20">Step 1</span>
            <div class="font-bold text-slate-900 mt-1 font-cinzel">Office Initiates</div>
            <p class="text-slate-500 text-[11px] leading-relaxed">Secretary registers incoming leader and starts process.</p>
        </div>
        <div class="bg-white p-4 rounded-2xl card-elevation border border-slate-200/80 space-y-1 relative overflow-hidden">
            <span class="text-[10px] font-bold text-[#DC2626] bg-[#DC2626]/10 px-2 py-0.5 rounded-full border border-[#DC2626]/20">Step 2</span>
            <div class="font-bold text-slate-900 mt-1 font-cinzel">Outgoing Checklist</div>
            <p class="text-slate-500 text-[11px] leading-relaxed">Outgoing president files running matters & traditions.</p>
        </div>
        <div class="bg-white p-4 rounded-2xl card-elevation border border-slate-200/80 space-y-1 relative overflow-hidden">
            <span class="text-[10px] font-bold text-[#DC2626] bg-[#DC2626]/10 px-2 py-0.5 rounded-full border border-[#DC2626]/20">Step 3</span>
            <div class="font-bold text-slate-900 mt-1 font-cinzel">Vault Verification</div>
            <p class="text-slate-500 text-[11px] leading-relaxed">Chaplaincy verifies vault repository completeness.</p>
        </div>
        <div class="bg-white p-4 rounded-2xl card-elevation border border-slate-200/80 space-y-1 relative overflow-hidden">
            <span class="text-[10px] font-bold text-[#DC2626] bg-[#DC2626]/10 px-2 py-0.5 rounded-full border border-[#DC2626]/20">Step 4</span>
            <div class="font-bold text-slate-900 mt-1 font-cinzel">Joint Meeting</div>
            <p class="text-slate-500 text-[11px] leading-relaxed">Formal pastoral counsel meeting with Chaplaincy.</p>
        </div>
        <div class="bg-white p-4 rounded-2xl card-elevation border border-slate-200/80 space-y-1 relative overflow-hidden">
            <span class="text-[10px] font-bold text-[#DC2626] bg-[#DC2626]/10 px-2 py-0.5 rounded-full border border-[#DC2626]/20">Step 5</span>
            <div class="font-bold text-slate-900 mt-1 font-cinzel">Access Transfer</div>
            <p class="text-slate-500 text-[11px] leading-relaxed">Administrator transfers official email & permissions.</p>
        </div>
    </div>

    <!-- Handovers List -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-hidden divide-y divide-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">Active & Historical Transitions</h3>
            <span class="text-xs text-slate-500 font-semibold">{{ $handovers->total() }} Records</span>
        </div>

        @forelse($handovers as $h)
            <div class="p-5 sm:p-6 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1.5">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded text-white" style="background-color: {{ $h->club?->color_hex ?? '#DC2626' }}">
                            {{ $h->club?->name }} ({{ $h->club?->code }})
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded">
                            {{ $h->academic_year }}
                        </span>
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase
                            {{ $h->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ str_replace('_', ' ', $h->status) }}
                        </span>
                    </div>

                    <h4 class="text-sm font-bold text-slate-900 font-cinzel">
                        {{ $h->outgoing_president_name }} &rarr; {{ $h->incoming_president_name }}
                    </h4>

                    <div class="text-xs text-slate-500 flex flex-wrap items-center gap-x-4">
                        <span>Official Email: <strong class="text-[#DC2626] font-mono">{{ $h->club?->official_email }}</strong></span>
                        <span>Incoming Contact: <span class="font-mono text-slate-700">{{ $h->incoming_president_email }}</span></span>
                    </div>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('handover.show', $h->id) }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white text-xs font-bold transition shadow-sm active:scale-95">
                        <span>Review Progress</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-slate-500 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#DC2626] flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">No Active Handovers</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">When executive tenure concludes, the Chaplaincy initiates a formal 5-step transition.</p>
                @if(auth()->user()->isOffice())
                    <div class="pt-1">
                        <button type="button" onclick="document.getElementById('newHandoverModal').classList.toggle('hidden')" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] text-white text-xs font-semibold shadow-xs cursor-pointer">
                            <svg class="w-3.5 h-3.5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Initiate Club Handover</span>
                        </button>
                    </div>
                @endif
            </div>
        @endforelse
    </div>

    @if($handovers->hasPages())
        <div>{{ $handovers->links() }}</div>
    @endif

</div>

<!-- Initiate Handover Modal -->
<div id="newHandoverModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden card-elevation">
        <div class="bg-[#DC2626] p-5 text-white flex items-center justify-between border-b border-white/10">
            <div>
                <span class="text-[10px] uppercase font-bold text-rose-200 tracking-wider">Chapel Governance</span>
                <h3 class="text-base font-bold text-white mt-0.5">Initiate Club Handover</h3>
            </div>
            <button type="button" onclick="document.getElementById('newHandoverModal').classList.add('hidden')" class="text-white/80 hover:text-white p-1 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('handover.store') }}" class="p-6 space-y-4 text-xs">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Select Club <span class="text-rose-600">*</span></label>
                <select name="club_id" required class="w-full text-xs p-3 rounded-xl border border-slate-300 bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
                    @foreach($clubs as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Academic Year <span class="text-rose-600">*</span></label>
                <input type="text" name="academic_year" value="2026/2027" required class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Incoming President Full Name <span class="text-rose-600">*</span></label>
                <input type="text" name="incoming_president_name" required placeholder="e.g. Farai Makoni" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Incoming President Official/Student Email <span class="text-rose-600">*</span></label>
                <input type="email" name="incoming_president_email" required placeholder="e.g. fmakoni@africau.edu" class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Incoming President Phone</label>
                <input type="text" name="incoming_president_phone" placeholder="+263 77 ..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('newHandoverModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-xs font-semibold hover:bg-slate-200 transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white text-xs font-bold shadow-md active:scale-95">Start Handover</button>
            </div>
        </form>
    </div>
</div>
@endsection
