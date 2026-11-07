{{-- Event detail panel with one-tap "add to my calendar" (Google, Apple/.ics, Outlook), WhatsApp share and copy.
     Each trigger carries a JSON payload in data-event and calls openEvent(this). --}}
<div id="eventDrawer" class="hidden fixed inset-0 z-50" role="dialog" aria-modal="true" aria-labelledby="evTitle">
    <div class="absolute inset-0 bg-ink/40 backdrop-blur-[2px] modal-backdrop" onclick="closeEvent()"></div>
    <div class="drawer-panel">
        <div class="relative px-6 pt-6 pb-5 border-b border-slate-100">
            <span id="evBar" class="absolute left-0 top-0 right-0 h-1.5" style="background:#DC2626" aria-hidden="true"></span>
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p id="evClub" class="text-[12.5px] font-semibold text-slate-500"></p>
                    <h3 id="evTitle" class="mt-1 text-[1.3rem] font-bold leading-snug text-slate-900 tracking-[-0.01em]"></h3>
                    <p id="evClash" class="hidden mt-2 badge badge-rose badge-dot">Overlaps another booking in this venue</p>
                </div>
                <button type="button" onclick="closeEvent()" class="topbar-action -mr-2 -mt-1 shrink-0" aria-label="Close">
                    <x-icon name="x" />
                </button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">
            <dl class="space-y-3 text-[14px]">
                <div class="flex gap-3"><dt class="shrink-0"><x-icon name="calendar" class="w-5 h-5 text-slate-400" /><span class="sr-only">Date</span></dt><dd id="evDate" class="font-semibold text-slate-900"></dd></div>
                <div class="flex gap-3"><dt class="shrink-0"><x-icon name="clock" class="w-5 h-5 text-slate-400" /><span class="sr-only">Time</span></dt><dd id="evTime" class="font-semibold text-slate-900 tabular-nums"></dd></div>
                <div class="flex gap-3"><dt class="shrink-0"><x-icon name="pin" class="w-5 h-5 text-slate-400" /><span class="sr-only">Venue</span></dt><dd id="evVenue" class="text-slate-800"></dd></div>
                <div class="flex gap-3"><dt class="shrink-0"><x-icon name="members" class="w-5 h-5 text-slate-400" /><span class="sr-only">Expected attendance</span></dt><dd id="evAttendance" class="text-slate-800"></dd></div>
            </dl>
            <p id="evDesc" class="text-[14px] text-slate-600 leading-relaxed bg-slate-50 rounded-xl p-4"></p>

            <div>
                <h4 class="text-[14px] font-bold text-slate-900">Add to my calendar</h4>
                <p class="text-[13px] text-slate-500 mt-0.5">Opens your calendar with everything filled in. Just press save.</p>
                <div class="mt-3 grid gap-2">
                    <a id="evGoogle" href="#" target="_blank" rel="noopener" class="app-choice">
                        <span class="app-choice-logo"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3" fill="#fff" stroke="#DADCE0"/><path d="M3 8h18" stroke="#4285F4" stroke-width="2.5"/><text x="12" y="18.2" text-anchor="middle" font-family="Arial, sans-serif" font-weight="700" font-size="8.5" fill="#1A73E8">31</text></svg></span>
                        <span class="min-w-0 flex-1"><span class="block text-[14px] font-semibold text-slate-900">Google Calendar</span><span class="block text-[12px] text-slate-500">Android phones &amp; Gmail</span></span>
                        <x-icon name="external" class="w-4 h-4 text-slate-400" />
                    </a>
                    <a id="evIcs" href="#" class="app-choice">
                        <span class="app-choice-logo"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="4" fill="#fff" stroke="#D1D1D6"/><text x="12" y="9" text-anchor="middle" font-family="-apple-system, Arial, sans-serif" font-weight="600" font-size="4.2" fill="#FF3B30">{{ strtoupper(now()->format('D')) }}</text><text x="12" y="18" text-anchor="middle" font-family="-apple-system, Arial, sans-serif" font-size="9" fill="#1C1C1E">{{ now()->format('j') }}</text></svg></span>
                        <span class="min-w-0 flex-1"><span class="block text-[14px] font-semibold text-slate-900">iPhone / Apple Calendar</span><span class="block text-[12px] text-slate-500">Also any other calendar app</span></span>
                        <x-icon name="download" class="w-4 h-4 text-slate-400" />
                    </a>
                    <a id="evOutlook" href="#" target="_blank" rel="noopener" class="app-choice">
                        <span class="app-choice-logo"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="5" width="12" height="14" rx="1.5" fill="#28A8EA"/><rect x="3" y="7" width="10" height="10" rx="1.5" fill="#0078D4"/><ellipse cx="8" cy="12" rx="2.4" ry="2.9" fill="none" stroke="#fff" stroke-width="1.5"/></svg></span>
                        <span class="min-w-0 flex-1"><span class="block text-[14px] font-semibold text-slate-900">Outlook</span><span class="block text-[12px] text-slate-500">University email · <span class="underline" data-outlook-personal>or Outlook.com</span></span></span>
                        <x-icon name="external" class="w-4 h-4 text-slate-400" />
                    </a>
                </div>
            </div>

            <div>
                <h4 class="text-[14px] font-bold text-slate-900">Tell others</h4>
                <div class="mt-2 flex flex-wrap gap-2">
                    <a id="evWhatsapp" href="#" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">
                        <svg viewBox="0 0 24 24" fill="#25D366" aria-hidden="true"><path d="M12 2a10 10 0 00-8.6 15.1L2 22l5-1.3A10 10 0 1012 2zm0 18.2a8.2 8.2 0 01-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1112 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1a6.7 6.7 0 01-3.3-2.9c-.2-.4.2-.4.7-1.3.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 00-.7.3 3 3 0 00-.9 2.2 5.2 5.2 0 001.1 2.7 11.8 11.8 0 004.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 001.8-1.2 2.2 2.2 0 00.1-1.2c0-.1-.2-.2-.4-.3z"/></svg>
                        Share on WhatsApp
                    </a>
                    <button type="button" id="evCopy" class="btn btn-secondary btn-sm"><x-icon name="link" /> Copy details</button>
                </div>
            </div>
        </div>

        <div id="evProposalWrap" class="hidden px-6 py-4 border-t border-slate-100">
            <a id="evProposal" href="#" class="btn btn-secondary btn-block">View Linked Proposal <x-icon name="arrow-right" /></a>
        </div>
    </div>
</div>

<script>
    (function () {
        let lastFocus = null;
        const $ = (id) => document.getElementById(id);

        window.openEvent = function (el) {
            let ev;
            try { ev = JSON.parse(el.dataset.event || '{}'); } catch (e) { return; }
            lastFocus = el;
            $('evTitle').textContent = ev.title || '';
            $('evClub').textContent = ev.club || '';
            $('evDate').textContent = ev.date || '';
            $('evTime').textContent = ev.time || '';
            $('evVenue').textContent = ev.venue || '';
            $('evAttendance').textContent = ev.attendance ? ev.attendance + ' people expected' : 'Attendance not specified';
            $('evDesc').textContent = ev.desc || 'An approved activity on the Kwang-Lim Chapel calendar.';
            $('evBar').style.background = ev.color || '#DC2626';
            $('evClash').classList.toggle('hidden', !ev.clash);
            const l = ev.links || {};
            $('evGoogle').href = l.google || '#';
            $('evIcs').href = l.ics || '#';
            $('evOutlook').href = l.outlook || '#';
            $('evOutlook').dataset.personal = l.outlookPersonal || '';
            $('evWhatsapp').href = l.whatsapp || '#';
            $('evCopy').dataset.text = l.text || '';
            $('evProposalWrap').classList.toggle('hidden', !ev.proposal);
            if (ev.proposal) $('evProposal').href = ev.proposal;
            $('eventDrawer').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            setTimeout(() => $('evGoogle').focus(), 50);
        };
        window.closeEvent = function () {
            $('eventDrawer').classList.add('hidden');
            document.body.style.overflow = '';
            if (lastFocus) lastFocus.focus();
        };

        // "or Outlook.com" inside the Outlook choice opens the personal-account version
        document.querySelector('[data-outlook-personal]').addEventListener('click', function (e) {
            const url = $('evOutlook').dataset.personal;
            if (!url) return;
            e.preventDefault();
            e.stopPropagation();
            window.open(url, '_blank', 'noopener');
        });

        $('evCopy').addEventListener('click', async function () {
            const text = this.dataset.text || '';
            try { await navigator.clipboard.writeText(text); }
            catch (e) {
                const t = document.createElement('textarea');
                t.value = text; document.body.appendChild(t); t.select();
                try { document.execCommand('copy'); } catch (err) {}
                t.remove();
            }
            const label = this.lastChild;
            const prev = label.textContent;
            label.textContent = ' Copied';
            setTimeout(() => { label.textContent = prev; }, 1800);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !$('eventDrawer').classList.contains('hidden')) closeEvent();
        });
    })();
</script>
