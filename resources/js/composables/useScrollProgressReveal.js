import { onMounted, onUnmounted, unref } from "vue";

const clamp = (value, min = 0, max = 1) => {
    if (Number.isNaN(value)) return min;
    return Math.min(max, Math.max(min, value));
};

const toNumber = (value, fallback) => {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
};

const toArray = (value) => {
    if (!value) return [];
    return Array.isArray(value) ? value : [value];
};

const isElementNode = (value) =>
    typeof Element !== "undefined" && value instanceof Element;

/**
 * Maps section scroll progress (0..1) to CSS variables on target elements.
 * Enhancement is automatically disabled for reduced-motion users.
 */
export function useScrollProgressReveal(options = {}) {
    const {
        sectionRef,
        targetRefs = [],
        selector = null,
        enabled = true,
        start = 0.15,
        end = 0.6,
        enhancedClass = "is-scroll-reveal-enhanced",
        progressVar = "--scroll-reveal-progress",
        inverseProgressVar = "--scroll-reveal-progress-inverse",
        progressPercentVar = "--scroll-reveal-progress-percent",
        inversePercentVar = "--scroll-reveal-progress-inverse-percent",
    } = options;

    let animationFrameId = null;
    let reducedMotion = false;
    let activeTargets = [];
    let removeMotionListener = null;

    const getEnabled = () => {
        const resolved = typeof enabled === "function" ? enabled() : unref(enabled);
        return Boolean(resolved);
    };

    const resolveElement = (value) => {
        const resolved = unref(value);
        if (isElementNode(resolved)) return resolved;
        if (resolved && isElementNode(resolved.$el)) return resolved.$el;
        return null;
    };

    const resolveTargets = () => {
        const targets = [];
        const seen = new Set();
        const resolvedTargetRefs =
            typeof targetRefs === "function" ? targetRefs() : targetRefs;

        toArray(resolvedTargetRefs).forEach((targetRef) => {
            const target = resolveElement(targetRef);
            if (target && !seen.has(target)) {
                seen.add(target);
                targets.push(target);
            }
        });

        if (selector) {
            const section = resolveElement(sectionRef);
            if (section) {
                section.querySelectorAll(selector).forEach((target) => {
                    if (!seen.has(target)) {
                        seen.add(target);
                        targets.push(target);
                    }
                });
            }
        }

        return targets;
    };

    const resetTargets = (targets) => {
        targets.forEach((target) => {
            target.classList.remove(enhancedClass);
            target.style.removeProperty(progressVar);
            target.style.removeProperty(inverseProgressVar);
            target.style.removeProperty(progressPercentVar);
            target.style.removeProperty(inversePercentVar);
        });
    };

    const syncTargets = (targets) => {
        const staleTargets = activeTargets.filter((target) => !targets.includes(target));
        if (staleTargets.length) resetTargets(staleTargets);
        activeTargets = targets;
    };

    const getProgress = () => {
        if (typeof window === "undefined") return 1;

        const section = resolveElement(sectionRef);
        if (!section) return 1;

        const rect = section.getBoundingClientRect();
        const viewportHeight = Math.max(window.innerHeight || 0, 1);
        const sectionProgress = clamp(
            (viewportHeight - rect.top) / (rect.height + viewportHeight)
        );

        const startValue = clamp(toNumber(unref(start), 0.15));
        const endValue = clamp(toNumber(unref(end), 0.6));
        const distance = endValue - startValue;

        if (distance === 0) {
            return sectionProgress >= endValue ? 1 : 0;
        }

        return clamp((sectionProgress - startValue) / distance);
    };

    const update = () => {
        animationFrameId = null;
        const targets = resolveTargets();
        syncTargets(targets);

        if (!targets.length) return;

        if (!getEnabled() || reducedMotion) {
            resetTargets(targets);
            return;
        }

        const progress = getProgress();
        const inverse = 1 - progress;
        const progressValue = progress.toFixed(4);
        const inverseValue = inverse.toFixed(4);
        const progressPercent = `${(progress * 100).toFixed(2)}%`;
        const inversePercent = `${(inverse * 100).toFixed(2)}%`;

        targets.forEach((target) => {
            target.classList.add(enhancedClass);
            target.style.setProperty(progressVar, progressValue);
            target.style.setProperty(inverseProgressVar, inverseValue);
            target.style.setProperty(progressPercentVar, progressPercent);
            target.style.setProperty(inversePercentVar, inversePercent);
        });
    };

    const queueUpdate = () => {
        if (typeof window === "undefined") return;
        if (animationFrameId !== null) return;
        animationFrameId = window.requestAnimationFrame(update);
    };

    onMounted(() => {
        if (typeof window === "undefined") return;

        const motionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
        const handleMotionChange = () => {
            reducedMotion = motionQuery.matches;
            queueUpdate();
        };

        handleMotionChange();

        if (typeof motionQuery.addEventListener === "function") {
            motionQuery.addEventListener("change", handleMotionChange);
            removeMotionListener = () =>
                motionQuery.removeEventListener("change", handleMotionChange);
        } else {
            motionQuery.addListener(handleMotionChange);
            removeMotionListener = () =>
                motionQuery.removeListener(handleMotionChange);
        }

        window.addEventListener("scroll", queueUpdate, { passive: true });
        window.addEventListener("resize", queueUpdate);
        queueUpdate();
    });

    onUnmounted(() => {
        if (typeof window !== "undefined") {
            window.removeEventListener("scroll", queueUpdate);
            window.removeEventListener("resize", queueUpdate);
            if (animationFrameId !== null) {
                window.cancelAnimationFrame(animationFrameId);
            }
        }

        if (removeMotionListener) {
            removeMotionListener();
            removeMotionListener = null;
        }

        resetTargets(activeTargets);
        activeTargets = [];
    });

    return {
        updateProgress: queueUpdate,
    };
}
