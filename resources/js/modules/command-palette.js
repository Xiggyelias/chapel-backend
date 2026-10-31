/**
 * Universal Command Palette (Ctrl + K) - Kwang-Lim Chapel Ministry Portal
 */

export const defaultQuickActionsHtml = `
    <div class="space-y-1 py-1">
        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block px-2 mb-1.5">Quick Actions</span>
        <a href="/requests/create" class="flex items-center space-x-2.5 p-2 rounded-xl hover:bg-slate-100 transition text-slate-700">
            <span class="w-6 h-6 rounded-lg bg-red-100 text-[#DC2626] flex items-center justify-center font-bold text-xs">📝</span>
            <span class="font-semibold text-xs text-slate-900">New Activity Proposal (14-Day Advance Form)</span>
        </a>
        <a href="/calendar" class="flex items-center space-x-2.5 p-2 rounded-xl hover:bg-slate-100 transition text-slate-700">
            <span class="w-6 h-6 rounded-lg bg-slate-100 text-[#DC2626] flex items-center justify-center font-bold text-xs">📅</span>
            <span class="font-semibold text-xs text-slate-900">Master Calendar (Sanctuary & Venues)</span>
        </a>
        <a href="/worship" class="flex items-center space-x-2.5 p-2 rounded-xl hover:bg-slate-100 transition text-slate-700">
            <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">📖</span>
            <span class="font-semibold text-xs text-slate-900">Call to Services & Projection Slides</span>
        </a>
        <a href="/hymns" class="flex items-center space-x-2.5 p-2 rounded-xl hover:bg-slate-100 transition text-slate-700">
            <span class="w-6 h-6 rounded-lg bg-red-100 text-[#DC2626] flex items-center justify-center font-bold text-xs">🎵</span>
            <span class="font-semibold text-xs text-slate-900">United Methodist Hymnal & Lyrics</span>
        </a>
        <a href="/vault" class="flex items-center space-x-2.5 p-2 rounded-xl hover:bg-slate-100 transition text-slate-700">
            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">🗄️</span>
            <span class="font-semibold text-xs text-slate-900">Records Vault & Archival Documents</span>
        </a>
        <a href="/clubs" class="flex items-center space-x-2.5 p-2 rounded-xl hover:bg-slate-100 transition text-slate-700">
            <span class="w-6 h-6 rounded-lg bg-rose-100 text-[#DC2626] flex items-center justify-center font-bold text-xs">🏛️</span>
            <span class="font-semibold text-xs text-slate-900">Fellowships Registry & Charter Directory</span>
        </a>
    </div>
`;

let searchTimeout = null;

export function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function safeUrl(value) {
    try {
        const u = new URL(String(value ?? ''), window.location.origin);
        return u.origin === window.location.origin ? u.href : '#';
    } catch (e) {
        return '#';
    }
}

export function openCommandPalette() {
    const modal = document.getElementById('commandPaletteModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    const input = document.getElementById('commandSearchInput');
    if (input) {
        input.value = '';
        input.focus();
    }
    const results = document.getElementById('commandSearchResults');
    if (results) {
        results.innerHTML = defaultQuickActionsHtml;
    }
}

export function handleCommandSearch(val) {
    clearTimeout(searchTimeout);
    const container = document.getElementById('commandSearchResults');
    if (!container) return;

    if (!val || val.length < 2) {
        container.innerHTML = defaultQuickActionsHtml;
        return;
    }

    searchTimeout = setTimeout(() => {
        fetch('/api/search?q=' + encodeURIComponent(val))
            .then(res => res.json())
            .then(data => {
                if (!data.results || data.results.length === 0) {
                    container.innerHTML = '<div class="text-slate-400 text-center py-6 text-xs">No matching records found.</div>';
                    return;
                }
                let html = '';
                data.results.forEach(item => {
                    // Search results contain user-supplied text: always escape before inserting
                    html += `
                        <a href="${escapeHtml(safeUrl(item.url))}" class="block p-2.5 rounded-xl hover:bg-slate-100 transition border border-transparent hover:border-slate-200">
                            <div class="flex items-center justify-between">
                                <div class="font-bold text-slate-900">${escapeHtml(item.title)}</div>
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-red-100 text-[#B91C1C] font-bold uppercase">${escapeHtml(item.category)}</span>
                            </div>
                            <div class="text-[11px] text-slate-600 mt-0.5">${escapeHtml(item.subtitle)}</div>
                        </a>
                    `;
                });
                container.innerHTML = html;
            })
            .catch(() => {
                container.innerHTML = '<div class="text-red-500 text-center py-4 text-xs">Error loading search results.</div>';
            });
    }, 250);
}

export function initCommandPaletteShortcuts() {
    window.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            openCommandPalette();
        }
        if (e.key === 'Escape') {
            const palette = document.getElementById('commandPaletteModal');
            if (palette && !palette.classList.contains('hidden')) {
                palette.classList.add('hidden');
            }
            const notif = document.getElementById('notificationDrawer');
            if (notif) notif.classList.add('hidden');
            const persona = document.getElementById('personaDropdown');
            if (persona) persona.classList.add('hidden');
            const moreNav = document.getElementById('moreNavDropdown');
            if (moreNav) moreNav.classList.add('hidden');
            const mobDrawer = document.getElementById('mobileDrawer');
            if (mobDrawer) mobDrawer.classList.add('hidden');
        }
    });
}
