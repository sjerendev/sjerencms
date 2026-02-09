<template>
    <section
        ref="sectionRef"
        :class="['sticky-carousel relative overflow-hidden', block.section_class]"
        :style="{ height: sectionHeight }"
        @keydown="handleKeydown"
        tabindex="0"
        aria-roledescription="carousel"
    >
        <div class="carousel-stage absolute inset-0" :style="stageStyle">
            <!-- Heading (optional) -->
            <div
                v-if="block.heading || block.subheading"
                class="absolute top-8 left-8 z-20 text-white"
            >
                <h2 v-if="block.heading" class="text-3xl md:text-5xl font-bold mb-2">
                    {{ block.heading }}
                </h2>
                <p v-if="block.subheading" class="text-lg md:text-xl opacity-80">
                    {{ block.subheading }}
                </p>
            </div>

            <!-- Desktop: Sticky scroll carousel -->
            <div
                v-if="!isMobile"
                ref="containerRef"
                class="carousel-container absolute inset-0"
            >
                <div
                    ref="trackRef"
                    class="carousel-track flex h-full"
                    :style="{ transform: `translateX(${translateX}px)` }"
                >
                    <a
                        v-for="(item, index) in safeImages"
                        :key="index"
                        :href="item.url"
                        class="carousel-slide flex-shrink-0 w-full h-full relative group"
                        :class="{ 'webgl-image-container': !isTouch }"
                    >
                        <img
                            :src="`/storage/${item.image}`"
                            :alt="item.title"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </a>
                </div>
            </div>

            <!-- Mobile: Touch carousel -->
            <div
                v-else
                ref="mobileContainerRef"
                class="mobile-carousel absolute inset-0 overflow-x-auto snap-x snap-mandatory scrollbar-hide"
                @scroll="handleMobileScroll"
            >
                <div class="flex h-full">
                    <a
                        v-for="(item, index) in safeImages"
                        :key="index"
                        :href="item.url"
                        class="carousel-slide flex-shrink-0 w-full h-full relative snap-center"
                    >
                        <img
                            :src="`/storage/${item.image}`"
                            :alt="item.title"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </a>
                </div>
            </div>

            <!-- Title handoff -->
            <div
                v-if="safeImages.length"
                class="title-handoff absolute left-8 bottom-24 z-30 text-white"
            >
                <div class="slide-title-stack">
                    <h3 class="slide-title slide-title-current" :style="currentTitleStyle">
                        {{ currentTitle }}
                    </h3>
                    <h3
                        v-if="showNextTitle"
                        class="slide-title slide-title-next"
                        :style="nextTitleStyle"
                    >
                        {{ nextTitle }}
                    </h3>
                </div>
            </div>

            <!-- Progress rail -->
            <div v-if="safeImages.length" class="progress-rail-wrap absolute bottom-8 left-1/2 -translate-x-1/2 z-30">
                <div class="progress-rail-track">
                    <div class="progress-rail-fill" :style="{ transform: `scaleX(${railProgress})` }"></div>
                </div>
                <div class="progress-rail-steps">
                    <button
                        v-for="(item, index) in safeImages"
                        :key="`step-${index}`"
                        type="button"
                        class="progress-step"
                        :class="{ 'is-active': index === currentIndex }"
                        :aria-label="`Go to slide ${index + 1}`"
                        @click="jumpToSlide(index)"
                    ></button>
                </div>
            </div>
        </div>

        <!-- Counter -->
        <div class="absolute bottom-8 right-8 z-20 text-white font-mono text-xl md:text-2xl">
            <span class="inline-block transition-all duration-300" :key="currentIndex + 1">
                {{ currentIndex + 1 }}
            </span>
            <span class="opacity-50">/</span>
            <span class="opacity-50">{{ totalImages }}</span>
        </div>

        <!-- Handoff gradient to next section -->
        <div class="handoff-veil absolute inset-x-0 bottom-0 z-20 pointer-events-none" :style="{ opacity: handoffProgress }"></div>

        <!-- Skip button -->
        <button
            v-if="showSkipButton && isSticky && !isMobile"
            @click="skipSection"
            class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm hover:bg-white/30 transition-colors"
        >
            Skip
        </button>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { useWebGLDistortion } from '@/js/composables/useWebGLDistortion.js';
import { useMotionPreferences } from '@/js/composables/useMotionPreferences.js';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});

// Refs
const sectionRef = ref(null);
const containerRef = ref(null);
const trackRef = ref(null);
const mobileContainerRef = ref(null);

// State
const isMobile = ref(false);
const isSticky = ref(false);
const showSkipButton = ref(false);
const currentIndex = ref(0);
const translateX = ref(0);
const isTouch = ref(false);
const overallProgress = ref(0);
const slideTransitionProgress = ref(0);
const handoffProgress = ref(0);
const { prefersReducedMotion } = useMotionPreferences();

const clamp = (value, min = 0, max = 1) => {
    if (Number.isNaN(value)) return min;
    return Math.min(max, Math.max(min, value));
};

const easeOutCubic = value => {
    const t = clamp(value);
    return 1 - Math.pow(1 - t, 3);
};

// Computed
const sectionHeight = computed(() => props.block.section_height || '100vh');
const safeImages = computed(() => Array.isArray(props.block.images) ? props.block.images : []);
const totalImages = computed(() => safeImages.value.length);
const maxIndex = computed(() => Math.max(totalImages.value - 1, 0));
const slideSpan = computed(() => Math.max(totalImages.value - 1, 0));
const easedTitleTransition = computed(() =>
    prefersReducedMotion.value ? 0 : easeOutCubic(slideTransitionProgress.value)
);
const currentTitle = computed(() => safeImages.value[currentIndex.value]?.title || '');
const nextIndex = computed(() => Math.min(currentIndex.value + 1, maxIndex.value));
const nextTitle = computed(() => safeImages.value[nextIndex.value]?.title || '');
const showNextTitle = computed(() =>
    nextIndex.value !== currentIndex.value && easedTitleTransition.value > 0
);
const railProgress = computed(() =>
    totalImages.value <= 1 ? 0 : clamp(overallProgress.value)
);

const currentTitleStyle = computed(() => {
    if (prefersReducedMotion.value) {
        return {
            opacity: '1',
            transform: 'translate3d(0, 0, 0)',
            filter: 'none'
        };
    }

    const transition = easedTitleTransition.value;
    return {
        opacity: `${1 - transition}`,
        transform: `translate3d(0, ${(-18 * transition).toFixed(2)}px, 0)`,
        filter: `blur(${(6 * transition).toFixed(2)}px)`
    };
});

const nextTitleStyle = computed(() => {
    if (prefersReducedMotion.value) {
        return {
            opacity: '1',
            transform: 'translate3d(0, 0, 0)',
            filter: 'none'
        };
    }

    const transition = easedTitleTransition.value;
    return {
        opacity: `${transition}`,
        transform: `translate3d(0, ${(20 * (1 - transition)).toFixed(2)}px, 0)`,
        filter: `blur(${(6 * (1 - transition)).toFixed(2)}px)`
    };
});

const stageStyle = computed(() => {
    if (prefersReducedMotion.value) return {};

    const shift = (handoffProgress.value * 22).toFixed(2);
    const scale = (1 - handoffProgress.value * 0.03).toFixed(4);
    const opacity = (1 - handoffProgress.value * 0.18).toFixed(4);
    return {
        transform: `translate3d(0, ${shift}px, 0) scale(${scale})`,
        opacity
    };
});

// WebGL distortion
const { init: initWebGL, destroy: destroyWebGL } = useWebGLDistortion({
    intensity: 1.0,
    containerRef: containerRef,
    imageSelector: '.webgl-image-container img'
});

// Check if mobile
const checkMobile = () => {
    if (typeof window === 'undefined') return;
    isMobile.value = window.innerWidth < 768;
};

const applyProgress = (progress, viewportWidth) => {
    const clampedProgress = clamp(progress);
    overallProgress.value = clampedProgress;

    const handoffStart = 0.82;
    handoffProgress.value = clamp(
        (clampedProgress - handoffStart) / (1 - handoffStart)
    );

    const span = slideSpan.value;
    if (!span) {
        currentIndex.value = 0;
        slideTransitionProgress.value = 0;
        translateX.value = 0;
        return;
    }

    const scaled = clampedProgress * span;
    const baseIndex = clamp(Math.floor(scaled), 0, maxIndex.value);
    const localProgress = clamp(scaled - baseIndex);

    currentIndex.value = baseIndex;
    slideTransitionProgress.value = baseIndex >= maxIndex.value ? 0 : localProgress;
    translateX.value = -(clampedProgress * span * viewportWidth);
};

const updateScrollPosition = () => {
    if (typeof window === 'undefined' || !sectionRef.value || isMobile.value) return;

    const rect = sectionRef.value.getBoundingClientRect();
    const viewportHeight = window.innerHeight;

    // Check if section is in sticky range
    if (rect.top <= 0 && rect.bottom >= viewportHeight) {
        isSticky.value = true;

        const stickyDistance = Math.max(rect.height - viewportHeight, 1);
        const scrollProgress = Math.abs(rect.top) / stickyDistance;
        applyProgress(scrollProgress, window.innerWidth);
    } else {
        isSticky.value = false;

        if (rect.top > 0) {
            applyProgress(0, window.innerWidth);
        } else {
            applyProgress(1, window.innerWidth);
        }
    }
};

const handleMobileScroll = (e) => {
    const container = e.target;
    const maxScrollable = Math.max(container.scrollWidth - container.clientWidth, 1);
    const progress = container.scrollLeft / maxScrollable;
    applyProgress(progress, container.clientWidth || window.innerWidth);
};

const jumpToSlide = index => {
    if (!sectionRef.value || !totalImages.value) return;
    const clampedIndex = clamp(index, 0, maxIndex.value);

    if (isMobile.value && mobileContainerRef.value) {
        const container = mobileContainerRef.value;
        const maxScrollable = Math.max(container.scrollWidth - container.clientWidth, 0);
        const targetLeft = maxIndex.value
            ? (clampedIndex / maxIndex.value) * maxScrollable
            : 0;
        container.scrollTo({
            left: targetLeft,
            behavior: prefersReducedMotion.value ? 'auto' : 'smooth'
        });
        return;
    }

    const sectionRect = sectionRef.value.getBoundingClientRect();
    const absoluteTop = window.scrollY + sectionRect.top;
    const stickyDistance = Math.max(sectionRect.height - window.innerHeight, 0);
    const targetProgress = maxIndex.value ? clampedIndex / maxIndex.value : 0;
    const targetTop = absoluteTop + stickyDistance * targetProgress;

    window.scrollTo({
        top: targetTop,
        behavior: prefersReducedMotion.value ? 'auto' : 'smooth'
    });
};

const handleKeydown = (e) => {
    if (e.key === 'ArrowRight' && currentIndex.value < maxIndex.value) {
        jumpToSlide(currentIndex.value + 1);
    } else if (e.key === 'ArrowLeft' && currentIndex.value > 0) {
        jumpToSlide(currentIndex.value - 1);
    }
};

const skipSection = () => {
    if (!sectionRef.value) return;
    const rect = sectionRef.value.getBoundingClientRect();
    window.scrollTo({
        top: window.scrollY + rect.bottom,
        behavior: prefersReducedMotion.value ? 'auto' : 'smooth'
    });
};

let skipButtonTimeout = null;
const startSkipButtonTimer = () => {
    if (skipButtonTimeout) clearTimeout(skipButtonTimeout);
    skipButtonTimeout = setTimeout(() => {
        showSkipButton.value = true;
    }, 2000);
};

const handleScroll = () => {
    updateScrollPosition();

    if (isSticky.value && !showSkipButton.value) {
        startSkipButtonTimer();
    }
};

const handleResize = () => {
    const wasMobile = isMobile.value;
    checkMobile();
    updateScrollPosition();

    if (wasMobile && !isMobile.value && !isTouch.value) {
        setTimeout(() => {
            initWebGL();
        }, 250);
    }

    if (!wasMobile && isMobile.value) {
        destroyWebGL();
    }
};

onMounted(async () => {
    checkMobile();
    isTouch.value = 'ontouchstart' in window;

    // Wait for DOM
    await nextTick();

    // Setup scroll listener
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleResize);

    updateScrollPosition();

    if (!isMobile.value && !isTouch.value) {
        setTimeout(() => {
            initWebGL();
        }, 500);
    }
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    window.removeEventListener('resize', handleResize);

    if (skipButtonTimeout) {
        clearTimeout(skipButtonTimeout);
    }

    destroyWebGL();
});
</script>

<style scoped>
.sticky-carousel {
    position: relative;
}

.carousel-container {
    position: sticky;
    top: 0;
}

.carousel-stage {
    transform-origin: center bottom;
    transition: transform 160ms linear, opacity 160ms linear;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.slide-title-stack {
    position: relative;
    min-height: clamp(2.2rem, 6vw, 4.4rem);
    min-width: min(70vw, 740px);
}

.slide-title {
    position: absolute;
    left: 0;
    bottom: 0;
    margin: 0;
    font-size: clamp(1.3rem, 4.2vw, 2.9rem);
    line-height: 1.04;
    font-weight: 700;
    text-wrap: balance;
    text-shadow: 0 10px 30px rgb(0 0 0 / 0.35);
    transition: opacity 140ms linear, transform 140ms linear, filter 140ms linear;
}

.progress-rail-wrap {
    width: min(68vw, 460px);
}

.progress-rail-track {
    width: 100%;
    height: 3px;
    border-radius: 9999px;
    background: rgb(255 255 255 / 0.25);
    overflow: hidden;
}

.progress-rail-fill {
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, #33ffd3 0%, #ffffff 100%);
    transform-origin: left center;
    transition: transform 120ms linear;
}

.progress-rail-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
}

.progress-step {
    width: 9px;
    height: 9px;
    border-radius: 9999px;
    border: 0;
    background: rgb(255 255 255 / 0.4);
    cursor: pointer;
    transition: transform 180ms ease, background-color 180ms ease, opacity 180ms ease;
}

.progress-step:hover {
    transform: scale(1.18);
    background: rgb(255 255 255 / 0.8);
}

.progress-step.is-active {
    background: #33ffd3;
    transform: scale(1.15);
}

.handoff-veil {
    height: clamp(120px, 22vh, 280px);
    background: linear-gradient(
        to bottom,
        rgb(16 24 31 / 0) 0%,
        rgb(16 24 31 / 0.56) 70%,
        rgb(16 24 31 / 0.96) 100%
    );
    transition: opacity 160ms linear;
}

.carousel-slide img {
    transition: transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
}

.carousel-slide:hover img {
    transform: scale(1.03);
}

@media (prefers-reduced-motion: reduce) {
    .carousel-stage,
    .slide-title,
    .progress-rail-fill,
    .handoff-veil {
        transition: none;
    }

    .carousel-track {
        transition: none;
    }

    .carousel-slide img {
        transition: none;
    }

    .carousel-slide:hover img {
        transform: none;
    }
}

@media (max-width: 767px) {
    .title-handoff {
        left: 1.25rem;
        bottom: 5.4rem;
    }

    .progress-rail-wrap {
        width: min(84vw, 360px);
    }
}
</style>
