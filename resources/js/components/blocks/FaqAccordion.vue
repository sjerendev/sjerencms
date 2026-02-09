<template>
    <section ref="sectionRef" :class="block.section_class">
        <div class="container py-12 lg:py-24 mx-auto px-6 2xl:px-0">
            <div
                v-if="block.heading || block.subheading"
                data-faq-reveal
                class="text-center mb-8"
            >
                <h2 class="text-3xl lg:text-5xl font-bold mb-4" v-if="block.heading">{{ block.heading }}</h2>
                <p class="text-gray-600" v-if="block.subheading">{{ block.subheading }}</p>
            </div>
            <div class="max-w-4xl mx-auto">
                <div
                    v-for="(item, index) in block.items"
                    :key="index"
                    :ref="(el) => setItemRef(index, el)"
                    data-faq-reveal
                    class="mb-4"
                >
                    <button
                        type="button"
                        @click="toggleItem(index)"
                        :aria-expanded="currentlyOpen === index"
                        :aria-controls="`faq-panel-${index}`"
                        class="flex justify-between items-center w-full px-4 py-3 text-left rounded-lg focus:outline-none faq-item"
                    >
                        <span class="font-medium text-2xl">{{ item.question }}</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': currentlyOpen === index }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        :id="`faq-panel-${index}`"
                        :style="getAnswerShellStyle(index)"
                        class="faq-answer-shell"
                    >
                        <div
                            :ref="(el) => setAnswerInnerRef(index, el)"
                            class="faq-answer-inner px-4 py-3 mt-2 bg-white rounded-lg"
                        >
                            <div v-html="getMarkdownContent(item.answer)" class="text-gray-600"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from "vue";
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import { useMotionPreferences } from "@/js/composables/useMotionPreferences.js";
import { useInViewReveal } from "@/js/composables/useInViewReveal.js";

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
})

const sectionRef = ref(null);
const currentlyOpen = ref(null);
const itemRefs = ref([]);
const answerInnerRefs = ref([]);
const answerHeights = ref({});
let removeResizeListener = null;

const { prefersReducedMotion } = useMotionPreferences();

const { observe } = useInViewReveal({
    itemSelector: "[data-faq-reveal]",
    once: true,
    stagger: 65,
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

const smartAutoScrollEnabled = computed(() => {
    const value =
        props.block.smart_auto_scroll ??
        props.block.auto_scroll ??
        props.block.accordion_auto_scroll ??
        false;

    return parseBoolean(value, false);
});

const setItemRef = (index, el) => {
    if (!el) return;
    itemRefs.value[index] = el;
};

const setAnswerInnerRef = (index, el) => {
    if (!el) return;
    answerInnerRefs.value[index] = el;
};

const measureAnswerHeight = (index) => {
    const target = answerInnerRefs.value[index];
    if (!target) return;
    answerHeights.value = {
        ...answerHeights.value,
        [index]: target.scrollHeight
    };
};

const measureAllAnswerHeights = () => {
    const nextHeights = {};
    answerInnerRefs.value.forEach((element, index) => {
        if (!element) return;
        nextHeights[index] = element.scrollHeight;
    });
    answerHeights.value = nextHeights;
};

const scrollOpenItemIntoView = async (index) => {
    if (index === null || index === undefined) return;
    if (!smartAutoScrollEnabled.value) return;

    await nextTick();

    const target = itemRefs.value[index];
    if (!target || typeof window === "undefined") return;

    const rect = target.getBoundingClientRect();
    const topPadding = 96;
    const bottomPadding = 120;
    const needsScrollUp = rect.top < topPadding;
    const needsScrollDown = rect.bottom > window.innerHeight - bottomPadding;

    if (!needsScrollUp && !needsScrollDown) return;

    const offset = needsScrollUp
        ? rect.top - topPadding
        : rect.bottom - (window.innerHeight - bottomPadding);

    window.scrollTo({
        top: window.scrollY + offset,
        behavior: prefersReducedMotion.value ? "auto" : "smooth",
    });
};

const toggleItem = (index) => {
    currentlyOpen.value = currentlyOpen.value === index ? null : index;
    nextTick(() => {
        measureAnswerHeight(index);
    });
};

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

const getAnswerShellStyle = (index) => {
    const isOpen = currentlyOpen.value === index;
    const targetHeight = answerHeights.value[index] ?? 0;

    if (prefersReducedMotion.value) {
        return {
            maxHeight: isOpen ? "none" : "0px",
            opacity: isOpen ? "1" : "0",
            filter: "none",
            transform: "none",
        };
    }

    return {
        maxHeight: isOpen ? `${targetHeight}px` : "0px",
        opacity: isOpen ? "1" : "0",
        filter: isOpen ? "blur(0px)" : "blur(6px)",
        transform: isOpen ? "translateY(0)" : "translateY(-6px)",
    };
};

watch(
    () => currentlyOpen.value,
    async (index) => {
        if (index === null) return;
        await nextTick();
        measureAnswerHeight(index);
        scrollOpenItemIntoView(index);
    }
);

watch(
    () => props.block.items,
    async () => {
        await nextTick();
        measureAllAnswerHeights();
    },
    { deep: true }
);

onMounted(async () => {
    observe(sectionRef);
    await nextTick();
    measureAllAnswerHeights();

    const onResize = () => {
        measureAllAnswerHeights();
    };

    window.addEventListener("resize", onResize);
    removeResizeListener = () => {
        window.removeEventListener("resize", onResize);
    };
});

onUnmounted(() => {
    removeResizeListener?.();
    removeResizeListener = null;
});
</script>

<style scoped>
.faq-answer-shell {
    overflow: hidden;
    transition: max-height 420ms cubic-bezier(0.22, 1, 0.36, 1),
        opacity 300ms ease,
        filter 300ms ease,
        transform 300ms ease;
    transform-origin: top center;
}

@media (prefers-reduced-motion: reduce) {
    .faq-answer-shell {
        transition: none;
    }
}
</style>
