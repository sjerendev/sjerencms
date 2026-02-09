<template>
    <section
        ref="sectionRef"
        :class="[block.section_class, 'py-16']"
        :style="backgroundStyle"
    >
        <div
            class="container px-4 mx-auto"
            :class="[
                block.text_alignment === 'left' ? 'text-left' :
                block.text_alignment === 'right' ? 'text-right' :
                'text-center'
            ]"
            :style="{ color: block.text_color }"
        >
            <h2
                ref="headlineRef"
                class="mb-4 text-3xl font-bold scroll-progress-reveal-target"
            >
                {{ block.headline }}
            </h2>
            <div
                ref="contentRef"
                v-html="getMarkdownContent(block.text)"
                class="mb-6 scroll-progress-reveal-target"
            ></div>
            <a
                ref="buttonRef"
                :href="block.button_url"
                class="inline-block px-8 py-3 font-semibold rounded-lg scroll-progress-reveal-target"
                :style="{
                    backgroundColor: block.text_color,
                    color: block.background_type === 'color' ? block.background_color : '#ffffff'
                }"
            >
                {{ block.button_text }}
            </a>
        </div>
    </section>
</template>

<script setup>
import { computed, ref } from 'vue'
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import { useScrollProgressReveal } from '@/js/composables/useScrollProgressReveal.js';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
})
const sectionRef = ref(null);
const headlineRef = ref(null);
const contentRef = ref(null);
const buttonRef = ref(null);

const backgroundStyle = computed(() => {
    if (props.block.background_type === 'image' && props.block.background_image) {
        return {
            backgroundImage: `url(/storage/${props.block.background_image})`,
            backgroundSize: 'cover',
            backgroundPosition: 'center'
        }
    }
    return {
        backgroundColor: props.block.background_color
    }
})

const textAlignmentClass = computed(() => ({
    'mx-auto': props.block.text_alignment === 'center',
    'ml-auto': props.block.text_alignment === 'right',
    'mr-auto': props.block.text_alignment === 'left'
}))

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

const parseBoolean = (value, fallback = false) => {
    if (typeof value === 'boolean') return value;
    if (typeof value === 'number') return value === 1;
    if (typeof value === 'string') {
        const normalized = value.trim().toLowerCase();
        if (['1', 'true', 'yes', 'on'].includes(normalized)) return true;
        if (['0', 'false', 'no', 'off'].includes(normalized)) return false;
    }
    return fallback;
};

const parseUnitInterval = (value, fallback) => {
    const parsed = Number.parseFloat(value);
    if (!Number.isFinite(parsed)) return fallback;
    return Math.min(1, Math.max(0, parsed));
};

const scrollRevealEnabled = computed(() => {
    const value =
        props.block.scroll_progress_reveal ??
        props.block.scroll_reveal ??
        props.block.enable_scroll_reveal ??
        props.block.reveal_on_scroll ??
        false;

    return parseBoolean(value, false);
});

const revealStart = computed(() =>
    parseUnitInterval(
        props.block.scroll_reveal_start ??
            props.block.scroll_progress_start ??
            props.block.reveal_start,
        0.15
    )
);

const revealEnd = computed(() =>
    parseUnitInterval(
        props.block.scroll_reveal_end ??
            props.block.scroll_progress_end ??
            props.block.reveal_end,
        0.6
    )
);

useScrollProgressReveal({
    sectionRef,
    targetRefs: [headlineRef, contentRef, buttonRef],
    enabled: scrollRevealEnabled,
    start: revealStart,
    end: revealEnd,
});
</script>

<style scoped>
.scroll-progress-reveal-target {
    --scroll-reveal-progress: 1;
    --scroll-reveal-progress-inverse: 0;
    --scroll-reveal-progress-percent: 100%;
    --scroll-reveal-progress-inverse-percent: 0%;
}

.scroll-progress-reveal-target.is-scroll-reveal-enhanced {
    opacity: var(--scroll-reveal-progress);
    clip-path: inset(0 var(--scroll-reveal-progress-inverse-percent) 0 0);
    -webkit-mask-image: linear-gradient(
        to right,
        #000 0%,
        #000 var(--scroll-reveal-progress-percent),
        transparent 100%
    );
    mask-image: linear-gradient(
        to right,
        #000 0%,
        #000 var(--scroll-reveal-progress-percent),
        transparent 100%
    );
    transition: opacity 120ms linear;
    will-change: opacity, clip-path, mask-image;
}

@media (prefers-reduced-motion: reduce) {
    .scroll-progress-reveal-target.is-scroll-reveal-enhanced {
        opacity: 1;
        clip-path: none;
        -webkit-mask-image: none;
        mask-image: none;
        transition: none;
    }
}
</style>
