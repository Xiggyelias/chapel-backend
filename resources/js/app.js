import './bootstrap';

import {
    toggleMobileDrawer,
    toggleMoreNav,
    toggleNotificationDrawer,
    togglePersonaDropdown,
    initNavigationListeners
} from './modules/navigation';

import {
    openCommandPalette,
    handleCommandSearch,
    initCommandPaletteShortcuts
} from './modules/command-palette';

import {
    toggleHighContrast,
    setLanguage,
    applyLanguage,
    updateA11yUI,
    resetAccessibility,
    initAccessibility
} from './modules/accessibility';

// layouts/app.blade.php ships its own role-aware handlers (and sets window.__chapelLayoutUi).
// Only expose the module versions where the page has not already defined them, so the
// module never silently overrides the layout's handlers or double-binds listeners.
const expose = (name, fn) => {
    if (typeof window[name] !== 'function') window[name] = fn;
};

expose('toggleMobileDrawer', toggleMobileDrawer);
expose('toggleMoreNav', toggleMoreNav);
expose('toggleNotificationDrawer', toggleNotificationDrawer);
expose('togglePersonaDropdown', togglePersonaDropdown);

expose('openCommandPalette', openCommandPalette);
expose('handleCommandSearch', handleCommandSearch);

expose('toggleHighContrast', toggleHighContrast);
expose('setLanguage', setLanguage);
expose('applyLanguage', applyLanguage);
expose('updateA11yUI', updateA11yUI);
expose('resetAccessibility', resetAccessibility);

const boot = () => {
    if (!window.__chapelLayoutUi) {
        initNavigationListeners();
        initCommandPaletteShortcuts();
        initAccessibility();
    }

    // Register Service Worker for PWA (single registration point for every page)
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
