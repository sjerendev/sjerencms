<template>
	<section ref="sectionRef" :class="block.section_class">
    <div class="container py-12 mx-auto">
        <div :class="`grid gap-8 card-pack-grid ${getColumnClass}`">
            <div
                v-for="(card, index) in block.cards"
                :key="index"
                class="card card-pack-item"
                data-card-pack-item
                data-card-pack-reveal
            >
                <img
                    v-if="card.image"
                    :src="`/storage/${card.image}`"
                    :alt="card.title"
                    class="object-cover w-full h-48 mb-4 card-pack-media"
                >
                <h3 class="mb-2 text-xl font-bold">{{ card.title }}</h3>
                <div v-html="getMarkdownContent(card.text)" class="mb-4"></div>
                <a
                    :href="card.link_url"
                    class="inline-block px-4 py-2 text-white rounded bg-primary-500"
                >
                    {{ card.link_text }}
                </a>
            </div>
        </div>
    </div>
	</section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import { useInViewReveal } from '@/js/composables/useInViewReveal.js';
import { useCardInteractionPack } from '@/js/composables/useCardInteractionPack.js';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});
const sectionRef = ref(null);

const getColumnClass = computed(() => {
    switch (props.block.columns) {
        case '3':
            return 'grid-cols-1 sm:grid-cols-3';
        case '4':
            return 'grid-cols-1 sm:grid-cols-4';
        case '5':
            return 'grid-cols-1 sm:grid-cols-5';
        default:
            return 'grid-cols-1 sm:grid-cols-3'; // Default to 3 columns if not specified
    }
});

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

const { observe } = useInViewReveal({
    itemSelector: '[data-card-pack-reveal]',
    once: true,
    stagger: 70
});

const { refresh: refreshCardInteractionPack } = useCardInteractionPack({
    sectionRef,
    itemSelector: '[data-card-pack-item]',
    maxTilt: 4,
    proximityRadius: 260
});

onMounted(() => {
    observe(sectionRef);
    refreshCardInteractionPack();
});
</script>
