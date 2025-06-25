<template>
    <section :class="block.section_class">
        <div class="container py-12 mx-auto">
            <div class="text-center mb-8" v-if="block.heading || block.subheading">
                <h2 class="text-3xl font-bold mb-4" v-if="block.heading">{{ block.heading }}</h2>
                <p class="text-gray-600" v-if="block.subheading">{{ block.subheading }}</p>
            </div>
            <div v-if="block.section_description" v-html="getMarkdownContent(block.section_description)" class="max-w-3xl mx-auto mb-12 text-center text-gray-500"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <div v-for="(member, index) in block.members" :key="index" 
                     class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="aspect-w-1 aspect-h-1">
                        <img :src="`/storage/${member.image}`" 
                             :alt="member.name"
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-1">{{ member.name }}</h3>
                        <p class="text-gray-600 mb-3">{{ member.position }}</p>
                        <div v-html="getMarkdownContent(member.bio)" class="mb-4 text-gray-600"></div>
                        <div class="flex space-x-4">
                            <a v-if="member.linkedin" :href="member.linkedin" target="_blank" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                </svg>
                            </a>
                            <a v-if="member.twitter" :href="member.twitter" target="_blank" class="text-blue-400 hover:text-blue-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
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
