@extends('layouts.app')

@section('title', 'Service Sheet: ' . $sheet->service_date->format('M d, Y'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header & Status -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('worship.index') }}" class="hover:underline text-[#DC2626] font-bold">Worship Planning</a>
                <span>/</span>
                <span class="text-slate-700 font-semibold">{{ $sheet->service_type }}</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                {{ $sheet->service_date->format('l, F j, Y') }}
            </h2>
            <div class="text-xs text-[#DC2626] font-bold mt-0.5">
                {{ $sheet->service_type }} &bull; Kwang-Lim Chapel Sanctuary
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('worship.slides', $sheet->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-[#DC2626] hover:bg-[#B91C1C] active:scale-95 text-white text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                <span>Projector Slides [F]</span>
            </a>
            <button type="button" onclick="dispatchWorshipDutyWhatsApp()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center space-x-1.5 active:scale-95 shadow-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.099.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.159.57 4.184 1.564 5.938l-1.564 5.714 5.86-1.537c1.7 1.011 3.684 1.595 5.803 1.595 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/></svg>
                <span>WhatsApp Duty Call</span>
            </button>
            <a href="{{ route('worship.bulletin', $sheet->id) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition border border-slate-300 flex items-center space-x-1.5 active:scale-95">
                <svg class="w-4 h-4 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Bulletin (PDF)</span>
            </a>
            @if(auth()->user()->isOffice())
                <a href="{{ route('worship.edit', $sheet->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-bold transition shadow-sm border border-slate-700 active:scale-95">
                    Edit Sheet
                </a>
            @endif
        </div>
    </div>

    <!-- Worship Service Policy 4.3 Automated Milestones Countdown Tracker -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 p-5 sm:p-6 space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center space-x-2 font-cinzel">
                <svg class="w-4 h-4 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span>Worship Service Policy (§4.3) Compliance Tracker</span>
            </h3>
            <span class="text-[11px] font-mono text-slate-500 font-semibold">Service: {{ $sheet->service_date->toDateString() }}</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
            @foreach($milestones as $key => $m)
                <div class="p-3.5 rounded-2xl border {{ $m['satisfied'] ? 'border-emerald-200 bg-emerald-50/50' : 'border-amber-200 bg-amber-50/40' }} flex items-start space-x-2.5 card-elevation">
                    @if($m['satisfied'])
                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] shrink-0 mt-0.5">✓</span>
                    @else
                        <span class="w-4 h-4 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">!</span>
                    @endif
                    <div class="text-xs">
                        <span class="font-bold {{ $m['satisfied'] ? 'text-emerald-900' : 'text-amber-900' }}">{{ $m['label'] }}</span>
                        <div class="text-[10px] text-slate-500 mt-0.5">Target Deadline: {{ $m['deadline']->format('D, M d') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Main Service Sheet Content Card -->
    <div class="bg-white rounded-3xl card-elevation border border-slate-200/80 overflow-hidden">
        <div class="bg-gradient-to-r from-[#991B1B] via-[#DC2626] to-[#B91C1C] text-white p-6 sm:p-7 border-b border-white/10">
            <span class="text-xs font-cinzel uppercase tracking-widest text-rose-200 font-bold">Liturgy & Order of Worship</span>
            <h2 class="text-2xl sm:text-3xl font-bold font-cinzel text-white mt-1">"{{ $sheet->theme ?: 'Worship Service' }}"</h2>
            <p class="text-xs text-rose-100/90 mt-1">Scripture Reading: <strong class="text-white">{{ $sheet->scripture ?: 'To be announced' }}</strong></p>
        </div>

        <div class="p-6 sm:p-7 space-y-6 text-xs divide-y divide-slate-100">
            <!-- Ministers & Liturgical Team -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-200/60">
                    <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Preacher</strong>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $sheet->preacher ?: 'University Chaplain' }}</span>
                </div>
                <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-200/60">
                    <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Liturgist</strong>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $sheet->liturgist ?: 'Assistant Chaplain' }}</span>
                </div>
                <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-200/60">
                    <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Bible Reader</strong>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $sheet->bible_reader ?: 'Student Representative' }}</span>
                </div>
                <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-200/60">
                    <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Choir</strong>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $sheet->choir ?: 'Praise the Lord Choir' }}</span>
                </div>
                <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-200/60">
                    <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Praise & Worship Team</strong>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $sheet->praise_worship_team ?: 'Chapel Worship Band' }}</span>
                </div>
                <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-200/60">
                    <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Media & Sound Contact</strong>
                    <span class="font-bold text-slate-900 text-sm mt-0.5 block font-mono">{{ $sheet->media_contact ?: 'media@africau.edu' }}</span>
                </div>
                <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-200/60 sm:col-span-3">
                    <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Sign-Language Interpreter</strong>
                    @if($sheet->sign_interpreter_required)
                        <span class="font-bold text-emerald-800 flex items-center space-x-1.5 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            <span>{{ $sheet->sign_interpreter_name ?: 'Chaplaincy Interpreter Confirmed' }}</span>
                        </span>
                    @else
                        <span class="text-slate-400 italic mt-0.5 block">Not requested</span>
                    @endif
                </div>
            </div>

            <!-- Hymns -->
            <div class="pt-5 space-y-1.5">
                <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Hymns of Praise & Dedication</strong>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-slate-800 leading-relaxed font-serif text-sm">
                    {{ $sheet->hymns ?: 'UMH 57: O For a Thousand Tongues to Sing; UMH 368: My Hope Is Built; Shona Hymn 11' }}
                </div>
            </div>

            <!-- Hymn & Worship Lyrics -->
            @if($sheet->lyrics)
                <div class="pt-5 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Hymn & Praise Lyrics</strong>
                        <button type="button" onclick="navigator.clipboard.writeText({{ json_encode($sheet->lyrics) }}); alert('Lyrics copied to clipboard!');" class="text-[11px] font-bold text-[#DC2626] hover:underline flex items-center space-x-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>Copy for Media Desk</span>
                        </button>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 text-slate-800 leading-relaxed font-mono text-xs whitespace-pre-wrap max-h-96 overflow-y-auto">
                        {{ $sheet->lyrics }}
                    </div>
                </div>
            @endif

            <!-- Ushers -->
            @if($sheet->ushers)
                <div class="pt-5 space-y-1.5">
                    <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Ushering Squad</strong>
                    <p class="text-slate-800 font-semibold bg-slate-50 p-3 rounded-xl border border-slate-200/80">{{ $sheet->ushers }}</p>
                </div>
            @endif

            <!-- Call to Services -->
            <div class="pt-5 space-y-1.5">
                <strong class="text-slate-400 uppercase tracking-wider text-[10px] block font-cinzel">Call to Services</strong>
                <p class="text-slate-700 bg-slate-50 p-4 rounded-2xl border border-slate-200/80 whitespace-pre-line leading-relaxed">
                    {{ $sheet->order_of_service_notes ?: 'Standard Methodist liturgical sequence with Call to Worship, Invocation, Scripture, Anthem, Sermon, Offertory, and Benediction.' }}
                </p>
            </div>
        </div>
    </div>

</div>

<script>
    function dispatchWorshipDutyWhatsApp() {
        const sheet = {{ Js::from([
            'service' => $sheet->service_type . ' • ' . $sheet->service_date->format('l, F j, Y'),
            'theme' => $sheet->theme ?: 'Chapel Assembly',
            'preacher' => $sheet->preacher ?: 'University Chaplain',
            'liturgist' => $sheet->liturgist ?: 'Assistant Chaplain',
            'reader' => $sheet->bible_reader ?: 'Student Representative',
            'choir' => $sheet->choir ?: ($sheet->praise_worship_team ?: 'Praise the Lord Choir'),
            'scripture' => $sheet->scripture ?: 'As Announced',
            'hymns' => $sheet->hymns ?: 'UMH / Shona Hymnody',
        ]) }};
        const text = `⛪ *KWANG-LIM CHAPEL*\n*OFFICIAL WORSHIP LITURGY DUTY CALL*\n\n📅 *Service:* ${sheet.service}\n📖 *Theme:* "${sheet.theme}"\n🎙️ *Preacher:* ${sheet.preacher}\n✝️ *Liturgist:* ${sheet.liturgist}\n📖 *Bible Reader:* ${sheet.reader}\n🎵 *Choir / Praise Team:* ${sheet.choir}\n📜 *Scripture Lesson:* ${sheet.scripture}\n🎶 *Hymns of Praise:* ${sheet.hymns}\n\n_Ministers, liturgists, and choir members are requested to report to the vestry 30 minutes prior for vestment and altar prayer._\n\nKwang-Lim Chapel Ministry Office`;
        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
    }
</script>
@endsection
