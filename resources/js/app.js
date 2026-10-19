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

// Expose functions to window for Blade onclick handlers and keyboard interactions
window.toggleMobileDrawer = toggleMobileDrawer;
window.toggleMoreNav = toggleMoreNav;
window.toggleNotificationDrawer = toggleNotificationDrawer;
window.togglePersonaDropdown = togglePersonaDropdown;

window.openCommandPalette = openCommandPalette;
window.handleCommandSearch = handleCommandSearch;

window.toggleHighContrast = toggleHighContrast;
window.setLanguage = setLanguage;
window.applyLanguage = applyLanguage;
window.updateA11yUI = updateA11yUI;
window.resetAccessibility = resetAccessibility;

// Initialize listeners on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    initNavigationListeners();
    initCommandPaletteShortcuts();
    initAccessibility();

    // Register Service Worker for PWA
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch((err) => {
            console.log('AU Chapel ServiceWorker notice:', err);
        });
    }
});
