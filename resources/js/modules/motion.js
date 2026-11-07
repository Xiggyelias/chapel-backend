/**
 * "Light & Time" — the portal's motion language.
 *
 *   Procession   page sections rise in, in order, like a processional
 *   Ink          display headings arrive soft and light, then take their weight
 *   Hymn board   numerals roll into place on reels, like the chapel's number cards
 *   Candlelight  a warm glow follows the pointer across cards
 *   Toll         pressing a primary action rings out once, like a bell
 *
 * All of it is progressive: without JS (or with reduced motion) the page is
 * simply there.
 */

const reduceMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ── Procession ─────────────────────────────────────────────────────────── */
function procession() {
    const targets = [
        ...document.querySelectorAll('#main-content > div > *'),
        ...document.querySelectorAll('[data-reveal]'),
    ].filter((n, i, all) => all.indexOf(n) === i && n.offsetParent !== null && !n.closest('[data-no-reveal]'));

    if (!targets.length) return;
    const vh = window.innerHeight;
    let firstScreen = 0;

    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (!e.isIntersecting) return;
            e.target.style.setProperty('--d', '0ms');
            e.target.classList.add('in');
            io.unobserve(e.target);
        });
    }, { rootMargin: '0px 0px -6% 0px' });

    targets.forEach((n) => {
        n.classList.add('rv');
        const top = n.getBoundingClientRect().top;
        if (top < vh) {
            const step = parseInt(n.dataset.reveal || '', 10);
            n.style.setProperty('--d', `${Number.isFinite(step) ? step : Math.min(firstScreen++, 8) * 80}ms`);
        } else {
            io.observe(n);
        }
    });

    // Two frames so the hidden state is painted before we release it
    requestAnimationFrame(() => requestAnimationFrame(() => {
        targets.forEach((n) => { if (n.getBoundingClientRect().top < vh) n.classList.add('in'); });
    }));

    // Clean up once settled so .rv never interferes with fixed/sticky children later
    setTimeout(() => targets.forEach((n) => { if (n.classList.contains('in')) n.classList.remove('rv', 'in'); }), 2600);
}

/* ── Ink settle ─────────────────────────────────────────────────────────── */
function ink() {
    const heads = document.querySelectorAll('[data-ink], .page-title');
    heads.forEach((h, hi) => {
        if (h.__ink) return;
        h.__ink = true;
        const cs = getComputedStyle(h);
        h.style.setProperty('--wght', cs.fontWeight);
        let i = 0;
        const base = parseInt(h.dataset.inkDelay || '0', 10) + hi * 120;
        const walker = document.createTreeWalker(h, NodeFilter.SHOW_TEXT);
        const texts = [];
        while (walker.nextNode()) texts.push(walker.currentNode);
        texts.forEach((node) => {
            if (!node.nodeValue.trim()) return;
            const frag = document.createDocumentFragment();
            node.nodeValue.split(/(\s+)/).forEach((part) => {
                if (!part) return;
                if (/^\s+$/.test(part)) { frag.appendChild(document.createTextNode(part)); return; }
                const s = document.createElement('span');
                s.className = 'ink-word';
                s.textContent = part;
                s.style.setProperty('--d', `${base + i++ * 70}ms`);
                frag.appendChild(s);
            });
            node.parentNode.replaceChild(frag, node);
        });
        requestAnimationFrame(() => requestAnimationFrame(() => h.classList.add('ink-in')));
    });
}

/* ── Hymn board reels ───────────────────────────────────────────────────── */
function reels(reduce) {
    const nodes = document.querySelectorAll('[data-reel], .stat-value');
    nodes.forEach((n) => {
        if (n.__reel || n.children.length) return;
        const text = n.textContent.trim();
        if (!/\d/.test(text) || text.length > 14) return;
        n.__reel = true;
        if (reduce) return;
        n.setAttribute('aria-label', text);
        n.textContent = '';
        const digits = [];
        [...text].forEach((ch) => {
            if (!/\d/.test(ch)) {
                const s = document.createElement('span');
                s.setAttribute('aria-hidden', 'true');
                s.textContent = ch;
                n.appendChild(s);
                return;
            }
            const reel = document.createElement('span');
            reel.className = 'reel';
            reel.setAttribute('aria-hidden', 'true');
            const col = document.createElement('span');
            col.className = 'reel-col';
            // A full turn of the card set, then on to the digit in the second set
            for (let k = 0; k < 20; k++) {
                const d = document.createElement('span');
                d.textContent = String(k % 10);
                col.appendChild(d);
            }
            reel.appendChild(col);
            n.appendChild(reel);
            digits.push([col, parseInt(ch, 10)]);
        });
        const spin = () => {
            digits.forEach(([col, v], idx) => {
                // Left to right, each card drops in a beat after the last
                col.style.setProperty('--d', `${idx * 90 + 120}ms`);
                col.style.transform = `translateY(-${10 + v}em)`;
            });
        };
        const io = new IntersectionObserver(([e]) => {
            if (e.isIntersecting) { requestAnimationFrame(() => requestAnimationFrame(spin)); io.disconnect(); }
        });
        io.observe(n);
    });
}

/* ── Candlelight hover ──────────────────────────────────────────────────── */
function candlelight() {
    const SEL = '.spot, .dash-stat-card, .card, .card-elevation';
    let last = null;
    document.addEventListener('pointermove', (e) => {
        const card = e.target.closest?.(SEL);
        if (last && last !== card) last.style.removeProperty('--spot-a');
        last = card;
        if (!card) return;
        const r = card.getBoundingClientRect();
        card.style.setProperty('--mx', `${e.clientX - r.left}px`);
        card.style.setProperty('--my', `${e.clientY - r.top}px`);
        card.style.setProperty('--spot-a', '1');
    }, { passive: true });
    document.addEventListener('pointerleave', () => { if (last) last.style.removeProperty('--spot-a'); });
}

/* ── Bell toll ──────────────────────────────────────────────────────────── */
function toll() {
    const SEL = '.btn-primary, .btn-ink, .btn-gold, a.bg-brand, button.bg-brand';
    document.addEventListener('pointerdown', (e) => {
        const b = e.target.closest?.(SEL);
        if (!b || b.disabled) return;
        const r = b.getBoundingClientRect();
        const ring = document.createElement('span');
        ring.className = 'toll';
        ring.style.left = `${e.clientX - r.left}px`;
        ring.style.top = `${e.clientY - r.top}px`;
        if (getComputedStyle(b).position === 'static') b.style.position = 'relative';
        b.appendChild(ring);
        ring.addEventListener('animationend', () => ring.remove());
    });
}

/* ── Nave: keep its scroll position between pages ───────────────────────── */
function rememberNave() {
    const nav = document.querySelector('[data-nave-scroll]');
    if (!nav) return;
    try {
        const y = parseInt(sessionStorage.getItem('nave-y') || '0', 10);
        if (y) nav.scrollTop = y;
    } catch {}
    window.addEventListener('pagehide', () => { try { sessionStorage.setItem('nave-y', String(nav.scrollTop)); } catch {} });
}

export function initMotion() {
    const reduce = reduceMotion();
    document.documentElement.classList.toggle('motion', !reduce);
    rememberNave();
    reels(reduce);
    if (!reduce) {
        procession();
        ink();
        toll();
    }
    candlelight();
}
