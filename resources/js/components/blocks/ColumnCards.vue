<template>
    <section ref="sectionRef" :class="block.section_class">
        <div class="container py-12 mx-auto">
            <div :class="`grid gap-8 card-pack-grid ${getColumnClass}`">
                <component
                    :is="card.url ? 'a' : 'div'"
                    v-for="(card, index) in block.cards"
                    :key="index"
                    :href="card.url || undefined"
                    class="card group card-pack-item"
                    data-card-pack-item
                    data-card-pack-reveal
                    :class="{ 'cursor-pointer hover:shadow-lg transition-shadow': card.url }"
                >
                    <div
                        v-if="card.image"
                        class="relative overflow-hidden aspect-[4/3] mb-4"
                        :class="{ 'webgl-card-image': !isTouch }"
                    >
                        <img
                            :src="`/storage/${card.image}`"
                            :alt="card.title || 'Card image'"
                            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
                        >
                    </div>
                    <div v-html="getMarkdownContent(card.text)" class="prose prose-sm max-w-none"></div>
                </component>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import { useWebGLDistortion } from '@/js/composables/useWebGLDistortion.js';
import { useInViewReveal } from '@/js/composables/useInViewReveal.js';
import { useCardInteractionPack } from '@/js/composables/useCardInteractionPack.js';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});

// Refs
const sectionRef = ref(null);

// State
const isTouch = ref(false);

// Grid column classes based on column count
const getColumnClass = computed(() => {
    switch (String(props.block.columns)) {
        case '2':
            return 'grid-cols-1 sm:grid-cols-2';
        case '3':
            return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
        case '4':
            return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4';
        case '5':
            return 'grid-cols-1 sm:grid-cols-3 lg:grid-cols-5';
        default:
            return 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3';
    }
});

// Markdown parsing
const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

// Check for images to enable WebGL
const hasImages = computed(() => {
    return props.block.cards?.some(card => card.image);
});

const { observe } = useInViewReveal({
    itemSelector: '[data-card-pack-reveal]',
    once: true,
    stagger: 70
});

const { refresh: refreshCardInteractionPack } = useCardInteractionPack({
    sectionRef,
    itemSelector: '[data-card-pack-item]',
    maxTilt: 4,
    proximityRadius: 280
});

// WebGL distortion (scaled down intensity for cards)
let webglInstance = null;

const initWebGL = async () => {
    if (typeof window === 'undefined') return;
    if (isTouch.value) return;
    if (!hasImages.value) return;

    // Wait for refs to be populated
    await nextTick();

    const section = sectionRef.value;
    if (!section) return;

    const container = section.querySelector('.webgl-card-image');
    if (!container) return;

    // Keep distortion scoped to this section only.
    const containerWrapper = { value: section };

    const { init, destroy } = useWebGLDistortion({
        intensity: 0.6, // Scaled down for cards
        containerRef: containerWrapper,
        imageSelector: '.webgl-card-image img'
    });

    webglInstance = { init, destroy };

    // Small delay to ensure images are loaded
    setTimeout(() => {
        init();
    }, 300);
};

// Lifecycle
onMounted(() => {
    isTouch.value = typeof window !== 'undefined' && ('ontouchstart' in window || navigator.maxTouchPoints > 0);
    observe(sectionRef);
    refreshCardInteractionPack();

    if (!isTouch.value && hasImages.value) {
        initWebGL();
    }
});

onUnmounted(() => {
    if (webglInstance?.destroy) {
        webglInstance.destroy();
    }
});
</script>

<style scoped>
.webgl-card-image {
    position: relative;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    .card img {
        transition: none;
    }

    .group:hover img {
        transform: none;
    }
}
</style>
