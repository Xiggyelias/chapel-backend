/**
 * Navigation & Menu System - Kwang-Lim Chapel Ministry Portal
 */

export function toggleMobileDrawer() {
    const drawer = document.getElementById('mobileDrawer');
    if (drawer) drawer.classList.toggle('hidden');
}

export function toggleMoreNav(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const moreNav = document.getElementById('moreNavDropdown');
    const persona = document.getElementById('personaDropdown');
    const notif = document.getElementById('notificationDrawer');
    if (persona) persona.classList.add('hidden');
    if (notif) notif.classList.add('hidden');
    if (moreNav) moreNav.classList.toggle('hidden');
}

export function toggleNotificationDrawer(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const notif = document.getElementById('notificationDrawer');
    const persona = document.getElementById('personaDropdown');
    const moreNav = document.getElementById('moreNavDropdown');
    if (persona) persona.classList.add('hidden');
    if (moreNav) moreNav.classList.add('hidden');
    if (notif) notif.classList.toggle('hidden');
}

export function togglePersonaDropdown(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    const persona = document.getElementById('personaDropdown');
    const notif = document.getElementById('notificationDrawer');
    const moreNav = document.getElementById('moreNavDropdown');
    if (notif) notif.classList.add('hidden');
    if (moreNav) moreNav.classList.add('hidden');
    if (persona) persona.classList.toggle('hidden');
}

export function initNavigationListeners() {
    document.addEventListener('click', function(e) {
        const persona = document.getElementById('personaDropdown');
        const personaBtn = document.getElementById('personaButton');
        if (persona && !persona.classList.contains('hidden')) {
            if (personaBtn && !personaBtn.contains(e.target) && !persona.contains(e.target)) {
                persona.classList.add('hidden');
            }
        }

        const notif = document.getElementById('notificationDrawer');
        const notifBtn = document.getElementById('notificationButton');
        if (notif && !notif.classList.contains('hidden')) {
            if (notifBtn && !notifBtn.contains(e.target) && !notif.contains(e.target)) {
                notif.classList.add('hidden');
            }
        }

        const moreNav = document.getElementById('moreNavDropdown');
        if (moreNav && !moreNav.classList.contains('hidden')) {
            if (!moreNav.contains(e.target)) {
                moreNav.classList.add('hidden');
            }
        }
    });
}
