/**
 * The Liturgical Dial — the Christian year drawn as a ring of season arcs,
 * turned so that today sits at the top. The full dial (landing page) draws
 * itself in, tilts toward the pointer and turns gently as the page scrolls;
 * the mini dial lives in the nave.
 */
import { liturgicalYear, fmt } from './liturgy';

const NS = 'http://www.w3.org/2000/svg';
const el = (tag, attrs = {}, parent) => {
    const n = document.createElementNS(NS, tag);
    for (const [k, v] of Object.entries(attrs)) n.setAttribute(k, v);
    if (parent) parent.appendChild(n);
    return n;
};
const polar = (c, r, f) => {
    const a = (f * 360 - 90) * (Math.PI / 180);
    return [c + r * Math.cos(a), c + r * Math.sin(a)];
};
const arcPath = (c, r, f1, f2) => {
    const [x1, y1] = polar(c, r, f1);
    const [x2, y2] = polar(c, r, f2);
    const large = f2 - f1 > 0.5 ? 1 : 0;
    return `M ${x1.toFixed(2)} ${y1.toFixed(2)} A ${r} ${r} 0 ${large} 1 ${x2.toFixed(2)} ${y2.toFixed(2)}`;
};

function mountFull(host, reduce) {
    const ly = liturgicalYear();
    const C = 300;
    const R = 222;
    const GAP = 0.0022;
    const baseRot = -ly.position * 360;

    const svg = el('svg', { viewBox: '0 0 600 600', role: 'img', 'aria-label': `The liturgical year. Today falls in ${ly.current.name}.` });
    const defs = el('defs', {}, svg);
    const glow = el('filter', { id: 'dialGlow', x: '-50%', y: '-50%', width: '200%', height: '200%' }, defs);
    el('feGaussianBlur', { stdDeviation: '6', result: 'b' }, glow);
    const merge = el('feMerge', {}, glow);
    el('feMergeNode', { in: 'b' }, merge);
    el('feMergeNode', { in: 'SourceGraphic' }, merge);
    const halo = el('radialGradient', { id: 'dialHalo' }, defs);
    el('stop', { offset: '0%', 'stop-color': '#F1D58F', 'stop-opacity': '0.10' }, halo);
    el('stop', { offset: '60%', 'stop-color': '#F1D58F', 'stop-opacity': '0.03' }, halo);
    el('stop', { offset: '100%', 'stop-color': '#F1D58F', 'stop-opacity': '0' }, halo);

    el('circle', { cx: C, cy: C, r: 196, fill: 'url(#dialHalo)' }, svg);

    const wheel = el('g', { class: 'dial-wheel' }, svg);
    const fadeIns = [];

    // Outer scale: hairline + month marks
    fadeIns.push(el('circle', { cx: C, cy: C, r: 286, fill: 'none', stroke: '#D9B76C', 'stroke-opacity': '0.22', 'stroke-width': '1' }, wheel));
    fadeIns.push(el('circle', { cx: C, cy: C, r: 182, fill: 'none', stroke: '#fff', 'stroke-opacity': '0.07', 'stroke-width': '1' }, wheel));
    const months = el('g', {}, wheel);
    fadeIns.push(months);
    ly.months.forEach(({ date, at }) => {
        const [x1, y1] = polar(C, 280, at);
        const [x2, y2] = polar(C, 292, at);
        el('line', { x1, y1, x2, y2, stroke: '#D9B76C', 'stroke-opacity': '0.5', 'stroke-width': '1' }, months);
        const mid = at + 15 / ly.total;
        const [tx, ty] = polar(C, 270, mid);
        const deg = mid * 360 + baseRot;
        const flip = ((deg % 360) + 360) % 360 > 90 && ((deg % 360) + 360) % 360 < 270;
        const t = el('text', {
            x: tx, y: ty, 'text-anchor': 'middle', 'dominant-baseline': 'middle',
            transform: `rotate(${mid * 360 + (flip ? 180 : 0)} ${tx} ${ty})`,
            fill: '#fff', 'fill-opacity': '0.38', 'font-size': '9.5', 'font-weight': '700', 'letter-spacing': '2.2',
            'font-family': 'Plus Jakarta Sans, sans-serif',
        }, months);
        t.textContent = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'][date.getMonth()];
    });

    // Sunday ticks just inside the ring
    const ticks = el('g', {}, wheel);
    fadeIns.push(ticks);
    ly.sundays.forEach((f) => {
        const [x1, y1] = polar(C, 198, f);
        const [x2, y2] = polar(C, 204, f);
        el('line', { x1, y1, x2, y2, stroke: '#fff', 'stroke-opacity': '0.22', 'stroke-width': '1.2', 'stroke-linecap': 'round' }, ticks);
    });

    // Season arcs
    const arcs = ly.segments.map((s) => {
        const span = Math.max(s.to - s.from - GAP, 0.004);
        const isCurrent = s === ly.current;
        const p = el('path', {
            d: arcPath(C, R, s.from + GAP / 2, s.from + GAP / 2 + span),
            class: `dial-arc${isCurrent ? ' is-current' : ''}`,
            stroke: s.ink,
            'stroke-width': isCurrent ? 30 : 22,
            opacity: isCurrent ? 1 : 0.42,
            tabindex: '0',
            role: 'button',
            'aria-label': `${s.name}: ${fmt(s.start)} – ${fmt(s.end)}`,
        }, wheel);
        p.__season = s;
        // Season name sits outside the arc on the longer seasons
        if (s.to - s.from > 0.05) {
            const mid = (s.from + s.to) / 2;
            const [tx, ty] = polar(C, 250, mid);
            const deg = ((mid * 360 + baseRot) % 360 + 360) % 360;
            const flip = deg > 90 && deg < 270;
            const t = el('text', {
                x: tx, y: ty, 'text-anchor': 'middle', 'dominant-baseline': 'middle',
                transform: `rotate(${mid * 360 + (flip ? 180 : 0)} ${tx} ${ty})`,
                fill: s.ink, 'fill-opacity': isCurrent ? '0.95' : '0.55', 'font-size': '12.5', 'font-style': 'italic',
                'font-family': 'Fraunces, Georgia, serif',
            }, wheel);
            t.textContent = s.name;
            fadeIns.push(t);
        }
        return p;
    });

    // Today: a gold needle and a breathing jewel on the ring
    // Lives on the wheel at today's angle, so it rides along when the year turns
    const today = el('g', { transform: `rotate(${(ly.position * 360).toFixed(3)} ${C} ${C})` }, wheel);
    const [nx1, ny1] = polar(C, 150, 0);
    const [nx2, ny2] = polar(C, 256, 0);
    el('line', { x1: nx1, y1: ny1, x2: nx2, y2: ny2, stroke: '#F1D58F', 'stroke-width': '1.5', 'stroke-linecap': 'round', 'stroke-opacity': '0.9' }, today);
    const [jx, jy] = polar(C, R, 0);
    const jewel = el('circle', { cx: jx, cy: jy, r: '7', fill: '#F1D58F', filter: 'url(#dialGlow)' }, today);
    el('circle', { cx: jx, cy: jy, r: '2.6', fill: '#fff' }, today);
    const [lx, ly2] = polar(C, 300, 0);
    const label = el('text', {
        x: lx, y: ly2 - 4, 'text-anchor': 'middle', fill: '#F1D58F', 'font-size': '10', 'font-weight': '700', 'letter-spacing': '3',
        'font-family': 'Plus Jakarta Sans, sans-serif',
    }, today);
    label.textContent = 'TODAY';

    host.prepend(svg);

    // Centre text: current season, swapped for any season you point at
    const centre = host.querySelector('[data-dial-centre]');
    const nameEl = host.querySelector('[data-dial-name]');
    const metaEl = host.querySelector('[data-dial-meta]');
    const blurbEl = host.querySelector('[data-dial-blurb]');
    const restingMeta = ly.current.key === 'pentecost'
        ? 'Today'
        : `Week ${ly.weekOfSeason} of ${ly.weeksInSeason} · ${ly.next.name} in ${ly.daysToNext} day${ly.daysToNext === 1 ? '' : 's'}`;
    const show = (s, resting) => {
        if (!nameEl) return;
        nameEl.textContent = s.name;
        nameEl.style.color = s.ink;
        if (metaEl) metaEl.textContent = resting ? restingMeta : `${fmt(s.start)} – ${fmt(s.end)} · ${s.days} day${s.days === 1 ? '' : 's'}`;
        if (blurbEl) blurbEl.textContent = s.blurb;
        if (centre && !reduce) centre.animate([{ opacity: 0.2, filter: 'blur(3px)' }, { opacity: 1, filter: 'blur(0)' }], { duration: 380, easing: 'cubic-bezier(.16,1,.3,1)' });
    };
    show(ly.current, true);
    arcs.forEach((p) => {
        const enter = () => { arcs.forEach((a) => a.setAttribute('stroke-width', a === p ? 32 : (a.classList.contains('is-current') ? 30 : 22))); show(p.__season, false); };
        const leave = () => { arcs.forEach((a) => a.setAttribute('stroke-width', a.classList.contains('is-current') ? 30 : 22)); show(ly.current, true); };
        p.addEventListener('pointerenter', enter);
        p.addEventListener('focus', enter);
        p.addEventListener('pointerleave', leave);
        p.addEventListener('blur', leave);
    });

    const setRot = (deg) => wheel.setAttribute('transform', `rotate(${deg.toFixed(3)} ${C} ${C})`);

    if (reduce) { setRot(baseRot); return; }

    // Draw-in: the ring unspools season by season while the year turns into place
    arcs.forEach((p, i) => {
        const len = p.getTotalLength();
        p.style.strokeDasharray = `${len} ${len}`;
        p.style.strokeDashoffset = len;
        p.animate([{ strokeDashoffset: len }, { strokeDashoffset: 0 }], {
            duration: 900, delay: 250 + i * 140, easing: 'cubic-bezier(.65,0,.35,1)', fill: 'forwards',
        }).finished.then(() => { p.style.strokeDasharray = ''; p.style.strokeDashoffset = ''; }).catch(() => {});
    });
    fadeIns.forEach((n, i) => n.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 1200, delay: 600 + i * 40, fill: 'backwards' }));
    today.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 900, delay: 1700, easing: 'cubic-bezier(.16,1,.3,1)', fill: 'backwards' });
    jewel.animate([{ r: 7 }, { r: 9.5 }, { r: 7 }], { duration: 3200, iterations: Infinity, easing: 'ease-in-out', delay: 2600 });

    // Turning: an eased spin into place, then scroll turns the year a little further
    let current = baseRot + 75;
    let target = baseRot;
    let tiltX = 0, tiltY = 0, tx = 0, ty = 0;
    const startAt = performance.now() + 150;
    let running = true;
    const io = new IntersectionObserver(([e]) => { running = e.isIntersecting; if (running) requestAnimationFrame(loop); });
    io.observe(host);

    window.addEventListener('scroll', () => { target = baseRot - Math.min(window.scrollY * 0.05, 40); }, { passive: true });
    window.addEventListener('pointermove', (e) => {
        const r = host.getBoundingClientRect();
        tx = Math.max(-1, Math.min(1, (e.clientX - (r.left + r.width / 2)) / r.width));
        ty = Math.max(-1, Math.min(1, (e.clientY - (r.top + r.height / 2)) / r.height));
    }, { passive: true });

    function loop(now) {
        if (!running) return;
        const k = now < startAt ? 0 : 0.035;
        current += (target - current) * k;
        tiltX += (ty * -7 - tiltX) * 0.06;
        tiltY += (tx * 7 - tiltY) * 0.06;
        setRot(current);
        host.style.transform = `perspective(1400px) rotateX(${tiltX.toFixed(2)}deg) rotateY(${tiltY.toFixed(2)}deg)`;
        requestAnimationFrame(loop);
    }
    setRot(current);
    requestAnimationFrame(loop);
}

function mountMini(host, reduce) {
    const ly = liturgicalYear();
    const onPaper = host.dataset.tone === 'paper';
    const C = 20;
    const svg = el('svg', { viewBox: '0 0 40 40', 'aria-hidden': 'true' });
    const g = el('g', { transform: `rotate(${-ly.position * 360} ${C} ${C})` }, svg);
    ly.segments.forEach((s) => {
        const span = Math.max(s.to - s.from - 0.012, 0.01);
        const p = el('path', {
            d: arcPath(C, 15, s.from + 0.006, s.from + 0.006 + span), fill: 'none', stroke: onPaper ? s.paper : s.ink,
            'stroke-width': s === ly.current ? 4.5 : 3, opacity: s === ly.current ? 1 : (onPaper ? 0.25 : 0.35),
        }, g);
        if (!reduce) {
            const len = p.getTotalLength();
            p.animate([{ strokeDasharray: `0 ${len}` }, { strokeDasharray: `${len} 0` }], { duration: 900, delay: 200, easing: 'cubic-bezier(.65,0,.35,1)' });
        }
    });
    el('circle', { cx: C, cy: 5, r: 2.4, fill: onPaper ? '#B8892B' : '#F1D58F' }, svg);
    host.prepend(svg);
    const meta = host.parentElement?.querySelector('[data-dial-mini-meta]');
    if (meta) meta.textContent = ly.current.key === 'pentecost' ? 'Today' : `Week ${ly.weekOfSeason} · ${ly.next.name} in ${ly.daysToNext}d`;
}

export function initDials() {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    document.querySelectorAll('[data-dial]').forEach((host) => {
        if (host.__dial) return;
        host.__dial = true;
        try {
            host.dataset.dial === 'mini' ? mountMini(host, reduce) : mountFull(host, reduce);
        } catch (e) { /* decorative: never break the page */ }
    });
}

/** Dashboard: "Week 24 of 27 · Advent in 22 days" strip */
export function initSeasonProgress() {
    document.querySelectorAll('[data-season-progress]').forEach((host) => {
        const ly = liturgicalYear();
        const bar = host.querySelector('[data-season-bar]');
        const meta = host.querySelector('[data-season-meta]');
        const pct = Math.min(100, Math.round(((ly.today - ly.current.start) / 86400000 + 1) / ly.current.days * 100));
        if (meta) meta.textContent = ly.current.key === 'pentecost'
            ? 'The Day of Pentecost'
            : `Week ${ly.weekOfSeason} of ${ly.weeksInSeason} · ${ly.next.name} begins in ${ly.daysToNext} day${ly.daysToNext === 1 ? '' : 's'}`;
        if (bar) requestAnimationFrame(() => { bar.style.width = pct + '%'; });
    });
}
