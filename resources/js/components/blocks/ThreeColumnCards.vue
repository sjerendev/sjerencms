<template>
	<section :class="block.section_class">
    <div class="container py-12 mx-auto">
        <div :class="`grid gap-8 ${getColumnClass}`">
            <div v-for="(card, index) in block.cards" :key="index" class="card">
                <img
                    v-if="card.image"
                    :src="`/storage/${card.image}`"
                    :alt="card.title"
                    class="object-cover w-full h-48 mb-4"
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
import { computed } from 'vue';
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});

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
</script>
