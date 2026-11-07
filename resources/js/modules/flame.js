/**
 * The Living Flame — a procedural candle flame on <canvas>.
 *
 * It sways on layered noise, leans away from the air your pointer moves,
 * and exposes a small vocabulary the page can speak to it:
 *   flame.kindle(level)  steady brightness, 0..1
 *   flame.breathe()      a small draught (e.g. a keystroke)
 *   flame.flare()        a bright lift (e.g. on submit)
 *   flame.gutter()       a dip and recovery (e.g. a failed sign-in)
 * The flame also writes its brightness to --flame on a host element, so
 * the surrounding room can be lit by it.
 */

// Cheap smooth 1D noise: a sum of incommensurate sines
const noise = (x) => (Math.sin(x * 1.13) + Math.sin(x * 2.71 + 1.3) * 0.5 + Math.sin(x * 5.87 + 2.1) * 0.25) / 1.75;

export function createFlame(canvas, opts = {}) {
    const ctx = canvas.getContext('2d');
    const lightHost = opts.lightHost || null;
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const seed = Math.random() * 100;

    let W = 0, H = 0, dpr = 1;
    let t = 0;
    let level = opts.level ?? 0.75; // where brightness rests
    let bright = level;             // current brightness
    let surge = 0;                  // transient boosts (flare/gutter), decays to 0
    let lean = 0, leanVel = 0, wind = 0;
    let lastX = null, lastT = 0;
    let visible = true;
    let raf = 0;

    const resize = () => {
        const r = canvas.getBoundingClientRect();
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        W = r.width; H = r.height;
        canvas.width = Math.max(1, Math.round(W * dpr));
        canvas.height = Math.max(1, Math.round(H * dpr));
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        if (reduce) draw();
    };

    function flamePath(cx, by, fh, maxW, sx, sy, wob, phase) {
        const N = 24;
        const left = [], right = [];
        for (let i = 0; i <= N; i++) {
            const s = i / N;
            const y = by - s * fh * sy;
            const w = maxW * sx * Math.sin(Math.PI * Math.pow(s, 0.55)) * (1 - 0.08 * s);
            const off = lean * Math.pow(s, 1.7) * fh * 0.42
                + noise(t * 3.3 - s * 3.6 + seed + phase) * wob * Math.pow(s, 1.25) * maxW * 0.55;
            left.push([cx + off - w, y]);
            right.push([cx + off + w, y]);
        }
        const p = new Path2D();
        const bottom = by + maxW * sx * 0.22;
        p.moveTo(cx, bottom);
        p.quadraticCurveTo(left[0][0] - maxW * sx * 0.05, bottom, left[1][0], left[1][1]);
        for (let i = 1; i < N; i++) {
            const [x0, y0] = left[i];
            const [x1, y1] = left[i + 1];
            p.quadraticCurveTo(x0, y0, (x0 + x1) / 2, (y0 + y1) / 2);
        }
        p.lineTo(left[N][0], left[N][1]);
        for (let i = N; i > 1; i--) {
            const [x0, y0] = right[i];
            const [x1, y1] = right[i - 1];
            p.quadraticCurveTo(x0, y0, (x0 + x1) / 2, (y0 + y1) / 2);
        }
        p.quadraticCurveTo(right[0][0] + maxW * sx * 0.05, bottom, cx, bottom);
        return p;
    }

    function grad(by, top, stops) {
        const g = ctx.createLinearGradient(0, by, 0, top);
        stops.forEach(([o, c]) => g.addColorStop(o, c));
        return g;
    }

    function draw() {
        if (!W || !H) return;
        ctx.clearRect(0, 0, W, H);

        const flicker = noise(t * 6.1 + seed) * 0.5 + noise(t * 13.7 + seed * 2) * 0.22;
        const b = Math.max(0.15, bright + surge);
        const cx = W / 2;
        const by = H * (opts.base ?? 0.84);
        const fh = H * (opts.height ?? 0.6) * (0.72 + 0.32 * b) * (1 + flicker * 0.07);
        const maxW = Math.min(W * 0.2, fh * 0.2);
        const wob = 0.6 + (1 - Math.min(b, 1)) * 0.6 + Math.abs(wind) * 2;

        // Halo: the light the flame throws into the room
        ctx.globalCompositeOperation = 'lighter';
        const hy = by - fh * 0.45;
        const hr = Math.max(W, H) * 0.55 * (0.75 + 0.35 * b) * (1 + flicker * 0.04);
        const halo = ctx.createRadialGradient(cx + lean * fh * 0.15, hy, 0, cx, hy, hr);
        halo.addColorStop(0, `rgba(255, 196, 120, ${0.22 * b})`);
        halo.addColorStop(0.25, `rgba(255, 160, 80, ${0.08 * b})`);
        halo.addColorStop(1, 'rgba(255, 140, 60, 0)');
        ctx.fillStyle = halo;
        ctx.fillRect(0, 0, W, H);

        // Outer mantle: blue at the root, amber, then a smoky red tip
        ctx.globalCompositeOperation = 'source-over';
        ctx.filter = `blur(${Math.max(1, maxW * 0.09)}px)`;
        ctx.fillStyle = grad(by + maxW * 0.2, by - fh, [
            [0, 'rgba(70, 110, 255, 0.0)'],
            [0.06, `rgba(90, 120, 255, ${0.55 * Math.min(1, b)})`],
            [0.16, 'rgba(255, 150, 60, 0.85)'],
            [0.55, 'rgba(255, 112, 40, 0.7)'],
            [0.85, 'rgba(200, 60, 30, 0.3)'],
            [1, 'rgba(150, 40, 20, 0)'],
        ]);
        ctx.fill(flamePath(cx, by, fh, maxW, 1, 1, wob, 0));

        // Body
        ctx.globalCompositeOperation = 'lighter';
        ctx.filter = `blur(${Math.max(0.6, maxW * 0.05)}px)`;
        ctx.fillStyle = grad(by, by - fh * 0.85, [
            [0, 'rgba(255, 190, 110, 0.0)'],
            [0.12, 'rgba(255, 205, 120, 0.85)'],
            [0.6, 'rgba(255, 170, 70, 0.55)'],
            [1, 'rgba(255, 140, 50, 0)'],
        ]);
        ctx.fill(flamePath(cx, by - fh * 0.03, fh, maxW, 0.7, 0.84, wob * 0.85, 0.7));

        // White-hot core
        ctx.filter = `blur(${Math.max(0.5, maxW * 0.04)}px)`;
        ctx.fillStyle = grad(by, by - fh * 0.6, [
            [0, 'rgba(255, 255, 255, 0)'],
            [0.15, `rgba(255, 252, 240, ${0.75 + 0.2 * Math.min(1, b)})`],
            [0.65, 'rgba(255, 236, 190, 0.5)'],
            [1, 'rgba(255, 220, 160, 0)'],
        ]);
        ctx.fill(flamePath(cx, by - fh * 0.06, fh, maxW, 0.36, 0.6, wob * 0.7, 1.4));
        ctx.filter = 'none';

        // The dark zone round the wick, and the wick itself
        ctx.globalCompositeOperation = 'source-over';
        const dz = ctx.createRadialGradient(cx, by - fh * 0.04, 0, cx, by - fh * 0.04, maxW * 0.5);
        dz.addColorStop(0, 'rgba(40, 30, 70, 0.55)');
        dz.addColorStop(1, 'rgba(40, 30, 70, 0)');
        ctx.fillStyle = dz;
        ctx.beginPath();
        ctx.ellipse(cx, by - fh * 0.04, maxW * 0.32, fh * 0.1, 0, 0, Math.PI * 2);
        ctx.fill();

        ctx.strokeStyle = '#2A1B14';
        ctx.lineWidth = Math.max(1.2, maxW * 0.09);
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.moveTo(cx, by + maxW * 0.45);
        ctx.quadraticCurveTo(cx, by, cx + lean * maxW * 0.5 + maxW * 0.08, by - fh * 0.07);
        ctx.stroke();
        ctx.fillStyle = 'rgba(255, 120, 50, 0.9)';
        ctx.beginPath();
        ctx.arc(cx + lean * maxW * 0.5 + maxW * 0.08, by - fh * 0.07, ctx.lineWidth * 0.55, 0, Math.PI * 2);
        ctx.fill();

        if (lightHost) lightHost.style.setProperty('--flame', (Math.min(1.4, b) * (1 + flicker * 0.08)).toFixed(3));
    }

    function step() {
        raf = 0;
        if (!visible) return;
        t += 1 / 60;
        bright += (level - bright) * 0.03;
        surge *= 0.955;
        wind *= 0.94;
        // Spring the lean toward the wind
        leanVel += ((wind * 1.2) - lean) * 0.06;
        leanVel *= 0.86;
        lean = Math.max(-0.9, Math.min(0.9, lean + leanVel));
        draw();
        raf = requestAnimationFrame(step);
    }

    const onMove = (e) => {
        const now = performance.now();
        if (lastX !== null) {
            const dt = Math.max(8, now - lastT);
            const vx = (e.clientX - lastX) / dt; // px per ms
            const r = canvas.getBoundingClientRect();
            const dx = e.clientX - (r.left + r.width / 2);
            const dy = e.clientY - (r.top + r.height / 2);
            const dist = Math.hypot(dx, dy);
            const near = Math.max(0, 1 - dist / Math.max(420, r.width * 2.4));
            // Moving air drags the flame along; close air pushes it away
            wind += vx * 0.06 * (0.3 + near);
            wind += -Math.sign(dx) * near * 0.02;
            wind = Math.max(-0.9, Math.min(0.9, wind));
        }
        lastX = e.clientX; lastT = now;
    };

    const ro = new ResizeObserver(resize);
    ro.observe(canvas);
    resize();

    if (!reduce) {
        window.addEventListener('pointermove', onMove, { passive: true });
        const io = new IntersectionObserver(([e]) => {
            visible = e.isIntersecting && !document.hidden;
            if (visible && !raf) raf = requestAnimationFrame(step);
        });
        io.observe(canvas);
        document.addEventListener('visibilitychange', () => {
            visible = !document.hidden;
            if (visible && !raf) raf = requestAnimationFrame(step);
        });
        raf = requestAnimationFrame(step);
    } else {
        draw();
    }

    return {
        kindle(v) { level = Math.max(0.2, Math.min(1.1, v)); if (reduce) { bright = level; draw(); } },
        breathe() { wind += (Math.random() - 0.5) * 0.35; surge += 0.05; },
        flare() { surge = 0.6; wind *= 0.3; },
        gutter() { surge = -0.55; wind += (Math.random() > 0.5 ? 1 : -1) * 0.5; },
    };
}

/** Mount every [data-flame] canvas; returns the first instance (pages usually have one). */
export function initFlames() {
    let first = null;
    document.querySelectorAll('canvas[data-flame]').forEach((c) => {
        if (c.__flame) return;
        const lightHost = c.dataset.flameLight ? document.querySelector(c.dataset.flameLight) : null;
        try {
            c.__flame = createFlame(c, {
                lightHost,
                level: parseFloat(c.dataset.flameLevel || '0.75'),
                base: c.dataset.flameBase ? parseFloat(c.dataset.flameBase) : undefined,
                height: c.dataset.flameHeight ? parseFloat(c.dataset.flameHeight) : undefined,
            });
            first = first || c.__flame;
        } catch (e) { /* decorative */ }
    });
    window.chapelFlame = first;
    return first;
}
