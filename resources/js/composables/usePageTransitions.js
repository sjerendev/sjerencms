import { nextTick } from 'vue';
import { gsap } from 'gsap';

const hasViewTransitionSupport = () =>
    typeof document !== 'undefined' &&
    typeof document.startViewTransition === 'function';

const hasReducedMotionPreference = () =>
    typeof window !== 'undefined' &&
    typeof window.matchMedia === 'function' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const runOnce = callback => {
    let called = false;
    return () => {
        if (called) return;
        called = true;
        callback();
    };
};

export function setupRouteViewTransitions(router) {
    if (typeof window === 'undefined') return;
    if (!router) return;
    if (!hasViewTransitionSupport()) return;

    router.beforeResolve((to, from) => {
        if (to.fullPath === from.fullPath) return;
        if (hasReducedMotionPreference()) return;

        return new Promise(resolve => {
            const done = runOnce(resolve);
            const transition = document.startViewTransition(async () => {
                done();
                await nextTick();
            });

            transition.finished
                .catch(() => {})
                .finally(done);
        });
    });
}

export function usePageTransitions(options = {}) {
    const {
        durationIn = 0.5,
        durationOut = 0.3,
        yIn = 20,
        yOut = -20
    } = options;

    const shouldUseFallback = () =>
        !hasViewTransitionSupport() || hasReducedMotionPreference();

    const pageEnter = (el, done) => {
        if (!shouldUseFallback()) {
            done();
            return;
        }

        gsap.fromTo(
            el,
            { opacity: 0, y: yIn },
            { opacity: 1, y: 0, duration: durationIn, ease: 'power2.out', onComplete: done }
        );
    };

    const pageLeave = (el, done) => {
        if (!shouldUseFallback()) {
            done();
            return;
        }

        gsap.to(el, {
            opacity: 0,
            y: yOut,
            duration: durationOut,
            ease: 'power2.inOut',
            onComplete: done
        });
    };

    return {
        pageEnter,
        pageLeave,
        shouldUseFallback
    };
}
