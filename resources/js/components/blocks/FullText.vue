<template>
    <section ref="sectionRef" :class="block.section_class">
        <div class="container py-16 lg:py-24 mx-auto px-6 2xl:px-0">
            <div
                ref="contentRef"
                class="scroll-progress-reveal-target"
                v-html="parsedContent"
            ></div>
        </div>
    </section>
</template>

<script setup>
import { marked } from "marked";
import { computed, ref } from "vue";
import DOMPurify from "dompurify";
import { useScrollProgressReveal } from "@/js/composables/useScrollProgressReveal.js";

const props = defineProps({
    block: {
        type: Object,
        required: true,
    },
});
const sectionRef = ref(null);
const contentRef = ref(null);

const parsedContent = computed(() => {
    if (!props.block.text) return "";
    const htmlContent = marked.parse(props.block.text);
    return DOMPurify.sanitize(htmlContent);
});

const parseBoolean = (value, fallback = false) => {
    if (typeof value === "boolean") return value;
    if (typeof value === "number") return value === 1;
    if (typeof value === "string") {
        const normalized = value.trim().toLowerCase();
        if (["1", "true", "yes", "on"].includes(normalized)) return true;
        if (["0", "false", "no", "off"].includes(normalized)) return false;
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
    targetRefs: [contentRef],
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
