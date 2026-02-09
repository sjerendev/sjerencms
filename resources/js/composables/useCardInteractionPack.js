import { onMounted, onUnmounted, unref } from "vue";

const clamp = (value, min = 0, max = 1) => {
    if (Number.isNaN(value)) return min;
    return Math.min(max, Math.max(min, value));
};

const toNumber = (value, fallback) => {
    const parsed = Number.parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
};

const isElementNode = (value) =>
    typeof Element !== "undefined" && value instanceof Element;

/**
 * Adds desktop-first proximity highlight + subtle micro-tilt on card grids.
 * Automatically disables on touch/coarse pointers and reduced-motion users.
 */
export function useCardInteractionPack(options = {}) {
    const {
        sectionRef,
        itemSelector = "[data-card-pack-item]",
        enabled = true,
        maxTilt = 4,
        proximityRadius = 260,
        enhancedClass = "is-card-pack-enhanced",
        hoveredClass = "is-card-pack-hovered",
        tiltXVar = "--card-pack-tilt-x",
        tiltYVar = "--card-pack-tilt-y",
        proximityVar = "--card-pack-proximity",
        pointerXVar = "--card-pack-pointer-x",
        pointerYVar = "--card-pack-pointer-y",
    } = options;

    let items = [];
    let animationFrameId = null;
    let pointerX = Number.NaN;
    let pointerY = Number.NaN;
    let reducedMotion = false;
    let coarsePointer = false;
    let removeMotionListener = null;
    let removePointerModeListener = null;
    let removeResizeListener = null;
    let removeSectionMoveListener = null;
    let removeSectionLeaveListener = null;
    const removeItemListeners = [];

    const getSection = () => {
        const resolved = unref(sectionRef);
        return isElementNode(resolved) ? resolved : null;
    };

    const getEnabled = () => {
        const resolved = typeof enabled === "function" ? enabled() : unref(enabled);
        return Boolean(resolved);
    };

    const shouldEnhance = () => getEnabled() && !reducedMotion && !coarsePointer;

    const setItemDefaults = (item) => {
        item.style.setProperty(tiltXVar, "0deg");
        item.style.setProperty(tiltYVar, "0deg");
        item.style.setProperty(proximityVar, "0");
        item.style.setProperty(pointerXVar, "50%");
        item.style.setProperty(pointerYVar, "50%");
    };

    const resetItem = (item) => {
        item.classList.remove(hoveredClass);
        item.classList.remove(enhancedClass);
        setItemDefaults(item);
    };

    const resetAll = () => {
        items.forEach(resetItem);
    };

    const clearItemListeners = () => {
        while (removeItemListeners.length) {
            const dispose = removeItemListeners.pop();
            dispose?.();
        }
    };

    const clearSectionListeners = () => {
        removeSectionMoveListener?.();
        removeSectionMoveListener = null;
        removeSectionLeaveListener?.();
        removeSectionLeaveListener = null;
    };

    const setProximity = () => {
        animationFrameId = null;
        if (!shouldEnhance()) {
            items.forEach((item) => {
                item.style.setProperty(proximityVar, "0");
            });
            return;
        }

        if (!Number.isFinite(pointerX) || !Number.isFinite(pointerY)) {
            items.forEach((item) => {
                item.style.setProperty(proximityVar, "0");
            });
            return;
        }

        const radius = Math.max(1, toNumber(unref(proximityRadius), 260));

        items.forEach((item) => {
            const rect = item.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            const distance = Math.hypot(pointerX - centerX, pointerY - centerY);
            const proximity = clamp(1 - distance / radius);
            item.style.setProperty(proximityVar, proximity.toFixed(4));
        });
    };

    const queueProximityUpdate = () => {
        if (typeof window === "undefined") return;
        if (animationFrameId !== null) return;
        animationFrameId = window.requestAnimationFrame(setProximity);
    };

    const bindSectionListeners = () => {
        const section = getSection();
        if (!section) return;

        const onMove = (event) => {
            pointerX = event.clientX;
            pointerY = event.clientY;
            queueProximityUpdate();
        };

        const onLeave = () => {
            pointerX = Number.NaN;
            pointerY = Number.NaN;
            items.forEach((item) => {
                item.style.setProperty(proximityVar, "0");
                item.classList.remove(hoveredClass);
                item.style.setProperty(tiltXVar, "0deg");
                item.style.setProperty(tiltYVar, "0deg");
            });
        };

        section.addEventListener("pointermove", onMove, { passive: true });
        section.addEventListener("pointerleave", onLeave);

        removeSectionMoveListener = () =>
            section.removeEventListener("pointermove", onMove);
        removeSectionLeaveListener = () =>
            section.removeEventListener("pointerleave", onLeave);
    };

    const bindItemListeners = () => {
        const tilt = Math.max(0, toNumber(unref(maxTilt), 4));

        items.forEach((item) => {
            const onEnter = () => {
                if (!shouldEnhance()) return;
                item.classList.add(hoveredClass);
            };

            const onMove = (event) => {
                if (!shouldEnhance()) {
                    item.style.setProperty(tiltXVar, "0deg");
                    item.style.setProperty(tiltYVar, "0deg");
                    return;
                }

                const rect = item.getBoundingClientRect();
                if (!rect.width || !rect.height) return;

                const localX = clamp((event.clientX - rect.left) / rect.width, 0, 1);
                const localY = clamp((event.clientY - rect.top) / rect.height, 0, 1);
                const centeredX = localX - 0.5;
                const centeredY = localY - 0.5;
                const tiltX = (-centeredY * tilt * 2).toFixed(2);
                const tiltY = (centeredX * tilt * 2).toFixed(2);

                item.style.setProperty(tiltXVar, `${tiltX}deg`);
                item.style.setProperty(tiltYVar, `${tiltY}deg`);
                item.style.setProperty(pointerXVar, `${(localX * 100).toFixed(2)}%`);
                item.style.setProperty(pointerYVar, `${(localY * 100).toFixed(2)}%`);
            };

            const onLeave = () => {
                item.classList.remove(hoveredClass);
                item.style.setProperty(tiltXVar, "0deg");
                item.style.setProperty(tiltYVar, "0deg");
                item.style.setProperty(pointerXVar, "50%");
                item.style.setProperty(pointerYVar, "50%");
            };

            item.addEventListener("pointerenter", onEnter, { passive: true });
            item.addEventListener("pointermove", onMove, { passive: true });
            item.addEventListener("pointerleave", onLeave);

            removeItemListeners.push(() => {
                item.removeEventListener("pointerenter", onEnter);
                item.removeEventListener("pointermove", onMove);
                item.removeEventListener("pointerleave", onLeave);
            });
        });
    };

    const applyEnhancementState = () => {
        items.forEach((item) => {
            if (shouldEnhance()) {
                item.classList.add(enhancedClass);
                return;
            }
            item.classList.remove(enhancedClass);
            item.classList.remove(hoveredClass);
            item.style.setProperty(tiltXVar, "0deg");
            item.style.setProperty(tiltYVar, "0deg");
            item.style.setProperty(proximityVar, "0");
        });
    };

    const refresh = () => {
        const section = getSection();
        items = section ? Array.from(section.querySelectorAll(itemSelector)) : [];

        clearItemListeners();
        items.forEach(setItemDefaults);
        bindItemListeners();
        applyEnhancementState();
    };

    onMounted(() => {
        if (typeof window === "undefined") return;

        const motionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
        const pointerQuery = window.matchMedia("(hover: none), (pointer: coarse)");

        const handleModeChange = () => {
            reducedMotion = motionQuery.matches;
            coarsePointer = pointerQuery.matches;
            applyEnhancementState();
            queueProximityUpdate();
        };

        handleModeChange();

        if (typeof motionQuery.addEventListener === "function") {
            motionQuery.addEventListener("change", handleModeChange);
            removeMotionListener = () =>
                motionQuery.removeEventListener("change", handleModeChange);
        } else {
            motionQuery.addListener(handleModeChange);
            removeMotionListener = () =>
                motionQuery.removeListener(handleModeChange);
        }

        if (typeof pointerQuery.addEventListener === "function") {
            pointerQuery.addEventListener("change", handleModeChange);
            removePointerModeListener = () =>
                pointerQuery.removeEventListener("change", handleModeChange);
        } else {
            pointerQuery.addListener(handleModeChange);
            removePointerModeListener = () =>
                pointerQuery.removeListener(handleModeChange);
        }

        const onResize = () => queueProximityUpdate();
        window.addEventListener("resize", onResize);
        removeResizeListener = () => window.removeEventListener("resize", onResize);

        bindSectionListeners();
        refresh();
        queueProximityUpdate();
    });

    onUnmounted(() => {
        if (typeof window !== "undefined" && animationFrameId !== null) {
            window.cancelAnimationFrame(animationFrameId);
        }

        clearItemListeners();
        clearSectionListeners();
        removeResizeListener?.();
        removeResizeListener = null;
        removeMotionListener?.();
        removeMotionListener = null;
        removePointerModeListener?.();
        removePointerModeListener = null;
        resetAll();
        items = [];
    });

    return {
        refresh,
        reset: resetAll,
    };
}
