import { defineMotion } from '../motion';

// Port of HeroWaves.tsx. The waveforms behind the hero, like an oscilloscope:
// a carrier in the accent colour and two overtones in ink drifting out of
// phase. The pointer steers the height (y), the speed (x) and a local bulge
// where it stands, as if you pushed the curve up with a finger.
//
// Drawn in canvas, not SVG: the curve is recomputed every frame, and a path
// with 700 points in the DOM would cost a lot more.
const LAYERS = [
    { amp: 0.22, freq: 1.9, speed: 0.00042, alpha: 0.8, width: 3, tone: 'accent' },
    { amp: 0.13, freq: 4.3, speed: -0.00061, alpha: 1, width: 1, tone: 'mint' },
    { amp: 0.06, freq: 8.9, speed: 0.00088, alpha: 0.24, width: 1.5 },
];

/** Width of the bulge around the pointer, as a share of the window width. */
const BULGE = 0.16;

function rgb(value) {
    const hex = value.trim().replace('#', '');
    const full = hex.length === 3 ? [...hex].map((c) => c + c).join('') : hex;
    const n = parseInt(full, 16);
    return [(n >> 16) & 255, (n >> 8) & 255, n & 255].join(',');
}

defineMotion('hero-waves', (el) => {
    const ctx = el.getContext('2d');
    if (!ctx) return;

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let colors = { accent: '252,111,93', ink: '17,24,32' };
    // A finished colour from the CSS: it changes per theme, see app.css.
    let waveMid = '#111820';

    const readColors = () => {
        const style = getComputedStyle(document.documentElement);
        colors = {
            accent: rgb(style.getPropertyValue('--accent')),
            ink: rgb(style.getPropertyValue('--ink')),
        };
        waveMid = style.getPropertyValue('--wave-mid').trim() || waveMid;
    };

    // The pointer is pulled towards its target instead of jumping there.
    const target = { x: 0.5, y: 0.5, on: 0 };
    const eased = { x: 0.5, y: 0.5, on: 0 };

    let width = 0;
    let height = 0;

    // The phase accumulates per frame. Computed as `time * speed`, every change
    // of speed would move the whole wave at once, and `time` is already tens of
    // thousands of milliseconds, so the jump would be huge.
    let phase = 0;
    let last = 0;

    const resize = () => {
        const box = el.getBoundingClientRect();
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        width = box.width;
        height = box.height;
        el.width = Math.round(width * dpr);
        el.height = Math.round(height * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    };

    const draw = (time) => {
        const dt = Math.min(time - last, 32);
        last = time;

        ctx.clearRect(0, 0, width, height);

        eased.x += (target.x - eased.x) * 0.06;
        eased.y += (target.y - eased.y) * 0.06;
        eased.on += (target.on - eased.on) * 0.05;

        const mid = height * 0.6;
        const lift = 0.75 + (1 - eased.y) * 0.7;
        // The pointer's x changes the speed within a narrow band, not the phase.
        phase += dt * (0.55 + eased.x * 0.45);

        for (const layer of LAYERS) {
            const amp = layer.amp * height * lift;
            ctx.beginPath();
            for (let x = 0; x <= width; x += 3) {
                const u = x / width;
                // The dome in the middle keeps the curve from being cut at the edges.
                const env = Math.sin(u * Math.PI);
                // The bulge under the pointer, a gaussian around its x.
                const d = (u - eased.x) / BULGE;
                const bulge = 1 + Math.exp(-d * d) * 1.05 * eased.on;
                const y =
                    mid +
                    (Math.sin(u * layer.freq * Math.PI * 2 + phase * layer.speed) +
                        Math.sin(u * layer.freq * 5.4 * Math.PI + phase * layer.speed * 1.7) * 0.28) *
                        amp *
                        env *
                        bulge;
                x === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
            }
            ctx.strokeStyle =
                layer.tone === 'mint'
                    ? waveMid
                    : `rgba(${layer.tone === 'accent' ? colors.accent : colors.ink},${layer.alpha})`;
            ctx.lineWidth = layer.width;
            ctx.stroke();
        }
    };

    const onPointer = (e) => {
        target.x = e.clientX / window.innerWidth;
        target.y = e.clientY / window.innerHeight;
        target.on = 1;
    };

    // theme.js sets data-theme on <html>; the colours follow it. Unlike Next,
    // the still frame is redrawn too, or it keeps the old theme's colours.
    const theme = new MutationObserver(() => {
        readColors();
        if (reduced) draw(0);
    });
    theme.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });

    readColors();
    resize();

    if (reduced) {
        // A still frame: the curves stay, but nothing moves.
        draw(0);

        const observer = new ResizeObserver(() => {
            resize();
            draw(0);
        });
        observer.observe(el);

        return () => {
            observer.disconnect();
            theme.disconnect();
        };
    }

    let frame = 0;
    const loop = (time) => {
        draw(time);
        frame = requestAnimationFrame(loop);
    };
    frame = requestAnimationFrame(loop);
    window.addEventListener('pointermove', onPointer, { passive: true });

    const observer = new ResizeObserver(resize);
    observer.observe(el);

    return () => {
        cancelAnimationFrame(frame);
        window.removeEventListener('pointermove', onPointer);
        observer.disconnect();
        theme.disconnect();
    };
});
