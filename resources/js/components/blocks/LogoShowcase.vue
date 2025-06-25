<template>
    <section :class="block.section_class">
        <div class="container py-12 mx-auto">
            <div class="text-center mb-8" v-if="block.heading || block.subheading">
                <h2 class="text-3xl font-bold mb-4" v-if="block.heading">{{ block.heading }}</h2>
                <p class="text-gray-600" v-if="block.subheading">{{ block.subheading }}</p>
            </div>
            <div v-if="block.section_description" v-html="getMarkdownContent(block.section_description)" class="max-w-3xl mx-auto mb-12 text-center text-gray-500"></div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-8 items-center">
                <div v-for="(logo, index) in block.logos" :key="index" 
                     class="flex items-center justify-center p-4 transition-opacity hover:opacity-75">
                    <a v-if="logo.url" :href="logo.url" target="_blank" class="block w-full">
                        <img :src="`/storage/${logo.image}`" 
                             :alt="logo.name"
                             class="max-w-full h-auto mx-auto"
                             :style="{ maxHeight: block.logo_height + 'px' }">
                    </a>
                    <img v-else
                         :src="`/storage/${logo.image}`" 
                         :alt="logo.name"
                         class="max-w-full h-auto mx-auto"
                         :style="{ maxHeight: block.logo_height + 'px' }">
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};
</script>
