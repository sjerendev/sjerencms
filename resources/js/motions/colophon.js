import gsap from 'gsap';
import { defineMotion } from '../motion';

/** Drift speed when the page stands still, in pixels per second. */
const DRIFT = 55;

/** Cap on how fast scrolling may pull the band, and how hard it kicks in. */
const MAX = 900;
const PUSH = 0.35;

// Port of ToolMarquee.tsx. The band drifts on its own and scrolling pushes it;
// scrolling up turns it around. The position is computed in a ticker, not a
// looping tween: a tween with repeat: -1 stops when it backs past its start.
defineMotion('colophon', (el) => {
    const mm = gsap.matchMedia();

    mm.add('(prefers-reduced-motion: no-preference)', () => {
        // The track holds the list twice. Half the width is one lap, and at the
        // lap change the band looks the same, so no seam shows.
        const lap = () => el.offsetWidth / 2 || 1;

        const drift = { speed: DRIFT };
        let x = 0;
        let settle;

        const tick = (_time, delta) => {
            x -= (drift.speed * delta) / 1000;
            const width = lap();
            if (x <= -width) x += width;
            if (x > 0) x -= width;
            gsap.set(el, { x });
        };

        let last = window.scrollY;
        let lastAt = performance.now();

        const onScroll = () => {
            const now = performance.now();
            const dt = Math.max(now - lastAt, 1);
            const velocity = ((window.scrollY - last) / dt) * 1000;
            last = window.scrollY;
            lastAt = now;

            const push = gsap.utils.clamp(-MAX, MAX, velocity * PUSH);
            if (Math.abs(push) < DRIFT) return;

            drift.speed = push;
            settle?.kill();
            settle = gsap.to(drift, { speed: DRIFT, duration: 0.9, ease: 'power2.out' });
        };

        gsap.ticker.add(tick);
        window.addEventListener('scroll', onScroll, { passive: true });

        return () => {
            window.removeEventListener('scroll', onScroll);
            gsap.ticker.remove(tick);
            settle?.kill();
            gsap.set(el, { clearProps: 'transform' });
        };
    });

    return () => mm.revert();
});
