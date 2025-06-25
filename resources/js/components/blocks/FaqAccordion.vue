<template>
    <section :class="block.section_class">
        <div class="container py-12 lg:py-24 mx-auto px-6 2xl:px-0">
            <div class="text-center mb-8" v-if="block.heading || block.subheading">
                <h2 class="text-3xl lg:text-5xl font-bold mb-4" v-if="block.heading">{{ block.heading }}</h2>
                <p class="text-gray-600" v-if="block.subheading">{{ block.subheading }}</p>
            </div>
            <div class="max-w-4xl mx-auto">
                <div v-for="(item, index) in block.items" :key="index" class="mb-4">
                    <button @click="toggleItem(index)"
                        class="flex justify-between items-center w-full px-4 py-3 text-left rounded-lg focus:outline-none faq-item">
                        <span class="font-medium text-2xl">{{ item.question }}</span>
                        <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': currentlyOpen === index }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="overflow-hidden transition-all duration-300 ease-in-out"
                        :style="{ maxHeight: currentlyOpen === index ? '500px' : '0', opacity: currentlyOpen === index ? '1' : '0' }">
                        <div class="px-4 py-3 mt-2 bg-white rounded-lg">
                            <div v-html="getMarkdownContent(item.answer)" class="text-gray-600"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from 'vue'
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
})

const currentlyOpen = ref(null);

const toggleItem = (index) => {
    currentlyOpen.value = currentlyOpen.value === index ? null : index;
};

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};
</script>

<style scoped>
.transition-all {
    transition-property: all;
}
</style>
