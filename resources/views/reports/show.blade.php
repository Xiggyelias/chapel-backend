@extends('layouts.app')

@section('title', $report->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('reports.index') }}" class="hover:underline text-[#DC2626] font-bold">Reports</a>
            <span>/</span>
            <span class="text-slate-700 font-mono">Report #{{ $report->id }}</span>
        </div>
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-bold font-cinzel text-slate-900 tracking-tight">
                {{ $report->title }}
            </h2>
            <span class="text-xs font-bold px-3 py-1 rounded-full uppercase
                {{ $report->status === 'reviewed' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                {{ $report->status }}
            </span>
        </div>
        <div class="text-xs text-slate-500 mt-0.5">
            Submitted by {{ $report->submitter->name }} ({{ $report->club->name }}) on {{ $report->created_at->format('M d, Y') }}
        </div>
    </div>

    <!-- Stats summary card -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-3xl card-elevation border border-slate-200/80">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-cinzel">Actual Attendance</span>
            <span class="text-2xl font-black font-cinzel text-[#DC2626] mt-0.5 block">{{ $report->attendance }} attendees</span>
        </div>
        <div class="bg-white p-5 rounded-3xl card-elevation border border-slate-200/80">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-cinzel">Total Expenditure</span>
            <span class="text-2xl font-black font-cinzel text-emerald-700 mt-0.5 block font-mono">${{ number_format($report->spending, 2) }}</span>
        </div>
        <div class="bg-white p-5 rounded-3xl card-elevation border border-slate-200/80 col-span-2 sm:col-span-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-cinzel">Sponsoring Club</span>
            <span class="text-sm font-bold text-slate-900 mt-0.5 block">{{ $report->club->name }}</span>
        </div>
    </div>

    <!-- Narrative Outcome -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-7 space-y-2 text-xs">
        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">Outcomes & Spiritual Impact Narrative</h3>
        <p class="text-slate-800 whitespace-pre-line leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
            {{ $report->outcome }}
        </p>
    </div>

    <!-- Photos Gallery (if any) -->
    @if(!empty($report->photos) && is_array($report->photos))
        <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-7 space-y-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">Activity Photo Gallery</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($report->photos as $photoIndex => $photo)
                    <a href="{{ route('reports.photo', [$report->id, $photoIndex]) }}" target="_blank" rel="noopener" class="block rounded-2xl overflow-hidden border border-slate-200 aspect-video hover:opacity-90 shadow-sm transition">
                        <img src="{{ route('reports.photo', [$report->id, $photoIndex]) }}" alt="Event photo {{ $photoIndex + 1 }}" loading="lazy" class="w-full h-full object-cover">
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Chaplaincy Office Remarks -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-6 sm:p-7 space-y-3 text-xs">
        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider font-cinzel">Chaplaincy Office Review Remarks</h3>
        @if($report->chaplaincy_remarks)
            <p class="p-4 bg-rose-50/60 rounded-2xl border border-rose-200/80 text-rose-950 whitespace-pre-line leading-relaxed">
                {{ $report->chaplaincy_remarks }}
            </p>
        @else
            <p class="text-slate-500 italic">No formal review remarks recorded yet.</p>
        @endif

        @if(auth()->user()->isOffice())
            <form method="POST" action="{{ route('reports.remarks', $report->id) }}" class="space-y-3 pt-3 border-t border-slate-100">
                @csrf
                <textarea name="chaplaincy_remarks" rows="2" required placeholder="Add chaplaincy commendation or follow-up note..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#DC2626]/20 focus:border-[#DC2626]">{{ $report->chaplaincy_remarks }}</textarea>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#DC2626] to-[#B91C1C] hover:brightness-110 text-white font-bold text-xs shadow-md active:scale-95">
                    Save Chaplaincy Remarks
                </button>
            </form>
        @endif
    </div>

</div>
@endsection
