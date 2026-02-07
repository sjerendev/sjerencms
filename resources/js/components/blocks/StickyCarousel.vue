<template>
    <section
        ref="sectionRef"
        :class="['sticky-carousel relative overflow-hidden', block.section_class]"
        :style="{ height: sectionHeight }"
        @keydown="handleKeydown"
        tabindex="0"
    >
        <!-- Heading (optional) -->
        <div v-if="block.heading || block.subheading" class="absolute top-8 left-8 z-20 text-white">
            <h2 v-if="block.heading" class="text-3xl md:text-5xl font-bold mb-2">{{ block.heading }}</h2>
            <p v-if="block.subheading" class="text-lg md:text-xl opacity-80">{{ block.subheading }}</p>
        </div>

        <!-- Desktop: Scroll-hijacking carousel -->
        <div
            v-if="!isMobile"
            ref="containerRef"
            class="carousel-container absolute inset-0"
        >
            <div
                ref="trackRef"
                class="carousel-track flex h-full transition-transform duration-100 ease-out"
                :style="{ transform: `translateX(${translateX}px)` }"
            >
                <a
                    v-for="(item, index) in block.images"
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
                    <!-- Title overlay - visible on hover -->
                    <div
                        class="absolute bottom-8 left-8 opacity-0 group-hover:opacity-100 transition-opacity duration-300 text-white"
                    >
                        <h3 class="text-2xl md:text-4xl font-bold">{{ item.title }}</h3>
                    </div>
                </a>
            </div>
        </div>

        <!-- Mobile: Touch-swipeable carousel -->
        <div
            v-else
            ref="mobileContainerRef"
            class="mobile-carousel absolute inset-0 overflow-x-auto snap-x snap-mandatory scrollbar-hide"
            @scroll="handleMobileScroll"
        >
            <div class="flex h-full">
                <a
                    v-for="(item, index) in block.images"
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
                    <div class="absolute bottom-8 left-8 text-white">
                        <h3 class="text-xl md:text-2xl font-bold">{{ item.title }}</h3>
                    </div>
                </a>
            </div>
        </div>

        <!-- Progress indicator -->
        <div class="absolute bottom-8 right-8 z-20 text-white font-mono text-xl md:text-2xl">
            <span class="inline-block transition-all duration-300" :key="currentIndex + 1">
                {{ currentIndex + 1 }}
            </span>
            <span class="opacity-50">/</span>
            <span class="opacity-50">{{ totalImages }}</span>
        </div>

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

// Computed
const sectionHeight = computed(() => props.block.section_height || '100vh');
const totalImages = computed(() => props.block.images?.length || 0);
const scrollPerImage = computed(() => typeof window !== 'undefined' ? window.innerHeight : 800);

// WebGL distortion
const { init: initWebGL, destroy: destroyWebGL, isTouch: webglIsTouch } = useWebGLDistortion({
    intensity: 1.0,
    containerRef: containerRef,
    imageSelector: '.webgl-image-container img'
});

// Check if mobile
const checkMobile = () => {
    if (typeof window === 'undefined') return;
    isMobile.value = window.innerWidth < 768;
};

// Calculate scroll position and update carousel
let lastScrollY = 0;
let sectionTop = 0;
let sectionHeight_px = 0;
let totalScrollDistance = 0;

const updateScrollPosition = () => {
    if (typeof window === 'undefined' || !sectionRef.value || isMobile.value) return;

    const rect = sectionRef.value.getBoundingClientRect();
    const viewportHeight = window.innerHeight;

    // Check if section is in sticky range
    if (rect.top <= 0 && rect.bottom >= viewportHeight) {
        isSticky.value = true;

        // Calculate progress through the sticky section
        const scrollProgress = Math.abs(rect.top) / (rect.height - viewportHeight);
        const clampedProgress = Math.max(0, Math.min(1, scrollProgress));

        // Calculate current image index
        const imageIndex = Math.floor(clampedProgress * totalImages.value);
        currentIndex.value = Math.min(imageIndex, totalImages.value - 1);

        // Calculate translate for smooth scrolling
        const slideWidth = window.innerWidth;
        translateX.value = -clampedProgress * (totalImages.value - 1) * slideWidth;
    } else {
        isSticky.value = false;

        // Snap to start or end
        if (rect.top > 0) {
            translateX.value = 0;
            currentIndex.value = 0;
        } else {
            translateX.value = -(totalImages.value - 1) * window.innerWidth;
            currentIndex.value = totalImages.value - 1;
        }
    }
};

// Handle mobile scroll
const handleMobileScroll = (e) => {
    const container = e.target;
    const slideWidth = container.clientWidth;
    currentIndex.value = Math.round(container.scrollLeft / slideWidth);
};

// Keyboard navigation
const handleKeydown = (e) => {
    if (isMobile.value) return;

    if (e.key === 'ArrowRight' && currentIndex.value < totalImages.value - 1) {
        currentIndex.value++;
        translateX.value = -currentIndex.value * window.innerWidth;
    } else if (e.key === 'ArrowLeft' && currentIndex.value > 0) {
        currentIndex.value--;
        translateX.value = -currentIndex.value * window.innerWidth;
    }
};

// Skip section
const skipSection = () => {
    if (!sectionRef.value) return;
    const rect = sectionRef.value.getBoundingClientRect();
    window.scrollTo({
        top: window.scrollY + rect.bottom,
        behavior: 'smooth'
    });
};

// Show skip button after delay
let skipButtonTimeout = null;
const startSkipButtonTimer = () => {
    if (skipButtonTimeout) clearTimeout(skipButtonTimeout);
    skipButtonTimeout = setTimeout(() => {
        showSkipButton.value = true;
    }, 2000);
};

// Scroll handler
const handleScroll = () => {
    updateScrollPosition();

    if (isSticky.value && !showSkipButton.value) {
        startSkipButtonTimer();
    }
};

// Resize handler
const handleResize = () => {
    checkMobile();
    updateScrollPosition();
};

// Lifecycle
onMounted(async () => {
    checkMobile();
    isTouch.value = 'ontouchstart' in window;

    // Wait for DOM
    await nextTick();

    // Setup scroll listener
    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleResize);

    // Initial position calculation
    updateScrollPosition();

    // Initialize WebGL after images load
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

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.carousel-slide img {
    transition: transform 0.3s ease;
}

.carousel-slide:hover img {
    transform: scale(1.02);
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
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
</style>
