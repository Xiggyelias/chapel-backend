{{--
    "Keep the chapel calendar on your phone" — one tap per calendar app, no files.

    Subscribing (rather than downloading) means new and changed activities
    appear on the person's own calendar automatically.

    <x-calendar-subscribe />            full version (calendar page, public page)
    <x-calendar-subscribe compact />    dashboard side panel
--}}
@props(['compact' => false, 'showQr' => true])

@php
    $feed = route('calendar.feed_ics');
    $webcal = preg_replace('#^https?://#', 'webcal://', $feed);
    $calName = 'Kwang-Lim Chapel';
    $links = [
        'google' => 'https://calendar.google.com/calendar/render?cid=' . rawurlencode($webcal),
        'apple' => $webcal,
        'outlook' => 'https://outlook.office.com/calendar/0/addfromweb?url=' . rawurlencode($feed) . '&name=' . rawurlencode($calName),
        'outlookPersonal' => 'https://outlook.live.com/calendar/0/addfromweb?url=' . rawurlencode($feed) . '&name=' . rawurlencode($calName),
    ];
    $uid = 'sub' . substr(md5($feed . ($compact ? 'c' : 'f') . spl_object_id($attributes)), 0, 6);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-3']) }}>
    @unless($compact)
        <p class="text-[14px] text-slate-600 leading-relaxed">Choose the calendar you already use. Chapel activities will appear there and <strong class="font-semibold text-slate-800">update on their own</strong> whenever the Chaplaincy approves or changes something.</p>
    @endunless

    <div class="grid gap-2 {{ $compact ? '' : 'sm:grid-cols-3' }}">
        <a href="{{ $links['google'] }}" target="_blank" rel="noopener" class="app-choice">
            <span class="app-choice-logo">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3" fill="#fff" stroke="#DADCE0"/><path d="M3 8h18" stroke="#4285F4" stroke-width="2.5"/><text x="12" y="18.2" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="8.5" fill="#1A73E8">31</text></svg>
            </span>
            <span class="min-w-0">
                <span class="block text-[14px] font-semibold text-slate-900">Google Calendar</span>
                <span class="block text-[12px] text-slate-500">Android phones &amp; Gmail</span>
            </span>
        </a>
        <a href="{{ $links['apple'] }}" class="app-choice">
            <span class="app-choice-logo">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="4" fill="#fff" stroke="#D1D1D6"/><text x="12" y="9" text-anchor="middle" font-family="-apple-system, Arial, sans-serif" font-weight="600" font-size="4.2" fill="#FF3B30">{{ strtoupper(now()->format('D')) }}</text><text x="12" y="18" text-anchor="middle" font-family="-apple-system, Arial, sans-serif" font-weight="400" font-size="9" fill="#1C1C1E">{{ now()->format('j') }}</text></svg>
            </span>
            <span class="min-w-0">
                <span class="block text-[14px] font-semibold text-slate-900">iPhone / Mac</span>
                <span class="block text-[12px] text-slate-500">Apple Calendar</span>
            </span>
        </a>
        <a href="{{ $links['outlook'] }}" target="_blank" rel="noopener" class="app-choice">
            <span class="app-choice-logo">
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="5" width="12" height="14" rx="1.5" fill="#28A8EA"/><path d="M9 9h12" stroke="#0078D4" stroke-width="1.2"/><rect x="3" y="7" width="10" height="10" rx="1.5" fill="#0078D4"/><ellipse cx="8" cy="12" rx="2.4" ry="2.9" fill="none" stroke="#fff" stroke-width="1.5"/></svg>
            </span>
            <span class="min-w-0">
                <span class="block text-[14px] font-semibold text-slate-900">Outlook</span>
                <span class="block text-[12px] text-slate-500">University email</span>
            </span>
        </a>
    </div>

    @if($showQr && !$compact)
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
            <x-qr-code :value="route('calendar.public')" :size="96" label="QR code that opens the chapel calendar on your phone" class="rounded-lg p-1.5 shrink-0" />
            <div class="min-w-0">
                <p class="text-[14px] font-semibold text-slate-900">On a computer right now?</p>
                <p class="text-[13px] text-slate-600 leading-relaxed mt-0.5">Point your phone's camera at this code. It opens the chapel calendar on your phone, where you can tap your calendar app above.</p>
            </div>
        </div>
    @endif

    <details class="group text-[13px]">
        <summary class="cursor-pointer list-none inline-flex items-center gap-1.5 font-semibold text-slate-500 hover:text-slate-800 select-none">
            <x-icon name="chevron-right" class="w-3.5 h-3.5 transition-transform group-open:rotate-90" />
            Other calendar apps &amp; help
        </summary>
        <div class="mt-3 space-y-3 text-slate-600 leading-relaxed">
            <p>Using a personal <a href="{{ $links['outlookPersonal'] }}" target="_blank" rel="noopener" class="font-semibold text-brand hover:underline">Outlook.com / Hotmail</a> account? Use that link instead.</p>
            <p>For any other app, choose <em>“Add calendar from internet / by URL”</em> in its settings and paste this link:</p>
            <div class="flex gap-2">
                <label for="{{ $uid }}" class="sr-only">Chapel calendar link</label>
                <input id="{{ $uid }}" type="text" readonly value="{{ $feed }}" onclick="this.select()" class="field min-w-0 flex-1 !py-2 !text-[12.5px] font-mono bg-white text-slate-600">
                <button type="button" class="btn btn-secondary btn-sm shrink-0"
                        onclick="(async (b) => { const v = document.getElementById('{{ $uid }}').value; try { await navigator.clipboard.writeText(v); } catch (e) { document.getElementById('{{ $uid }}').select(); document.execCommand('copy'); } b.textContent = 'Copied'; setTimeout(() => b.textContent = 'Copy link', 2000); })(this)">Copy link</button>
            </div>
            <p class="text-[12.5px] text-slate-500">Calendar apps refresh subscribed calendars on their own schedule. Google can take a few hours to show a new activity; Apple and Outlook are usually quicker.</p>
        </div>
    </details>
</div>
