/**
 * Accessibility & Multi-Language Preferences - Kwang-Lim Chapel
 */

export function toggleHighContrast() {
    const html = document.documentElement;
    const isHigh = html.classList.toggle('high-contrast');
    try {
        localStorage.setItem('au_high_contrast', isHigh ? 'true' : 'false');
    } catch (e) {}
    updateA11yUI();
}

export function setLanguage(lang) {
    try {
        localStorage.setItem('au_lang', lang);
    } catch (e) {}
    applyLanguage(lang);
    updateA11yUI();
}

export function applyLanguage(lang) {
    document.querySelectorAll('.nav-label').forEach(el => {
        const text = el.getAttribute('data-' + lang);
        if (text) {
            el.textContent = text;
        }
    });
    const badge = document.getElementById('currentLangBadge');
    if (badge) {
        badge.textContent = (lang === 'sn') ? 'ChiShona' : 'English';
    }
}

export function updateA11yUI() {
    const isHigh = document.documentElement.classList.contains('high-contrast');
    let lang = 'en';
    try {
        lang = localStorage.getItem('au_lang') || 'en';
    } catch (e) {}

    const hcBtn = document.getElementById('a11yHighContrastBtn');
    if (hcBtn) {
        hcBtn.textContent = isHigh ? 'Active (ON)' : 'Off';
        hcBtn.className = isHigh 
            ? 'px-3 py-1.5 rounded-lg text-xs font-bold border transition bg-emerald-600 text-white border-emerald-700' 
            : 'px-3 py-1.5 rounded-lg text-xs font-semibold border transition bg-slate-200 text-slate-700 hover:bg-slate-300';
    }

    const btnEn = document.getElementById('langBtnEn');
    const btnSn = document.getElementById('langBtnSn');
    if (btnEn && btnSn) {
        if (lang === 'sn') {
            btnSn.className = 'w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-[#DC2626] bg-[#DC2626] text-white shadow-xs';
            btnEn.className = 'w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-slate-300 bg-white text-slate-800 hover:bg-slate-50';
        } else {
            btnEn.className = 'w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-[#DC2626] bg-[#DC2626] text-white shadow-xs';
            btnSn.className = 'w-full py-2 px-3 rounded-xl text-xs font-bold transition border border-slate-300 bg-white text-slate-800 hover:bg-slate-50';
        }
    }
}

export function resetAccessibility() {
    try {
        localStorage.removeItem('au_high_contrast');
        localStorage.removeItem('au_large_text');
        localStorage.setItem('au_lang', 'en');
    } catch (e) {}
    document.documentElement.classList.remove('high-contrast');
    document.documentElement.classList.remove('large-text');
    applyLanguage('en');
    updateA11yUI();
}

export function initAccessibility() {
    try {
        localStorage.removeItem('au_large_text');
        document.documentElement.classList.remove('large-text');
        if (localStorage.getItem('au_high_contrast') === 'true') {
            document.documentElement.classList.add('high-contrast');
        }
    } catch (e) {}

    let lang = 'en';
    try {
        lang = localStorage.getItem('au_lang') || 'en';
    } catch (e) {}

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            applyLanguage(lang);
            updateA11yUI();
        });
    } else {
        applyLanguage(lang);
        updateA11yUI();
    }
}
