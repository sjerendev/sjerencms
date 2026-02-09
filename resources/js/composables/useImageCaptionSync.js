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
 * Sync image drift/zoom with caption reveal based on section scroll progress.
 */
export function useImageCaptionSync(options = {}) {
    const {
        sectionRef,
        imageRefs = [],
        captionRefs = [],
        imageSelector = null,
        captionSelector = null,
        enabled = true,
        start = 0.08,
        end = 0.72,
        imageTranslateY = 28,
        imageScaleFrom = 1.06,
        imageScaleTo = 1,
        captionTranslateY = 16,
        captionBlurFrom = 4,
        enhancedClass = "is-image-caption-sync-enhanced",
        progressVar = "--image-caption-sync-progress",
        imageTranslateVar = "--image-caption-sync-image-translate-y",
        imageScaleVar = "--image-caption-sync-image-scale",
        captionOpacityVar = "--image-caption-sync-caption-opacity",
        captionTranslateVar = "--image-caption-sync-caption-translate-y",
        captionBlurVar = "--image-caption-sync-caption-blur",
    } = options;

    let animationFrameId = null;
    let reducedMotion = false;
    let activeImageTargets = [];
    let activeCaptionTargets = [];
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

    const resolveTargets = (refs, selector) => {
        const targets = [];
        const seen = new Set();
        const section = resolveElement(sectionRef);
        const resolvedRefs = typeof refs === "function" ? refs() : refs;

        toArray(resolvedRefs).forEach((targetRef) => {
            const target = resolveElement(targetRef);
            if (target && !seen.has(target)) {
                seen.add(target);
                targets.push(target);
            }
        });

        if (selector && section) {
            section.querySelectorAll(selector).forEach((target) => {
                if (!seen.has(target)) {
                    seen.add(target);
                    targets.push(target);
                }
            });
        }

        return targets;
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

        const startValue = clamp(toNumber(unref(start), 0.08));
        const endValue = clamp(toNumber(unref(end), 0.72));
        const distance = endValue - startValue;

        if (distance === 0) {
            return sectionProgress >= endValue ? 1 : 0;
        }

        return clamp((sectionProgress - startValue) / distance);
    };

    const resetImageTargets = (targets) => {
        targets.forEach((target) => {
            target.classList.remove(enhancedClass);
            target.style.removeProperty(progressVar);
            target.style.removeProperty(imageTranslateVar);
            target.style.removeProperty(imageScaleVar);
        });
    };

    const resetCaptionTargets = (targets) => {
        targets.forEach((target) => {
            target.classList.remove(enhancedClass);
            target.style.removeProperty(progressVar);
            target.style.removeProperty(captionOpacityVar);
            target.style.removeProperty(captionTranslateVar);
            target.style.removeProperty(captionBlurVar);
        });
    };

    const syncTargetLists = (images, captions) => {
        const staleImages = activeImageTargets.filter(
            (target) => !images.includes(target)
        );
        const staleCaptions = activeCaptionTargets.filter(
            (target) => !captions.includes(target)
        );

        if (staleImages.length) resetImageTargets(staleImages);
        if (staleCaptions.length) resetCaptionTargets(staleCaptions);

        activeImageTargets = images;
        activeCaptionTargets = captions;
    };

    const update = () => {
        animationFrameId = null;

        const images = resolveTargets(imageRefs, imageSelector);
        const captions = resolveTargets(captionRefs, captionSelector);
        syncTargetLists(images, captions);

        if (!images.length && !captions.length) return;

        if (!getEnabled() || reducedMotion) {
            resetImageTargets(images);
            resetCaptionTargets(captions);
            return;
        }

        const progress = getProgress();
        const inverse = 1 - progress;
        const translateY = `${(inverse * toNumber(unref(imageTranslateY), 28)).toFixed(
            2
        )}px`;
        const scaleFrom = toNumber(unref(imageScaleFrom), 1.06);
        const scaleTo = toNumber(unref(imageScaleTo), 1);
        const scale = (scaleTo + (scaleFrom - scaleTo) * inverse).toFixed(4);
        const captionTranslate = `${(
            inverse * toNumber(unref(captionTranslateY), 16)
        ).toFixed(2)}px`;
        const captionBlur = `${(
            inverse * toNumber(unref(captionBlurFrom), 4)
        ).toFixed(2)}px`;
        const progressValue = progress.toFixed(4);

        images.forEach((target) => {
            target.classList.add(enhancedClass);
            target.style.setProperty(progressVar, progressValue);
            target.style.setProperty(imageTranslateVar, translateY);
            target.style.setProperty(imageScaleVar, scale);
        });

        captions.forEach((target) => {
            target.classList.add(enhancedClass);
            target.style.setProperty(progressVar, progressValue);
            target.style.setProperty(captionOpacityVar, progressValue);
            target.style.setProperty(captionTranslateVar, captionTranslate);
            target.style.setProperty(captionBlurVar, captionBlur);
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

        resetImageTargets(activeImageTargets);
        resetCaptionTargets(activeCaptionTargets);
        activeImageTargets = [];
        activeCaptionTargets = [];
    });

    return {
        updateProgress: queueUpdate,
    };
}
