<template>
    <section :class="block.section_class">
        <div class="container py-12 mx-auto">
            <div class="text-center mb-8" v-if="block.section_title || block.section_description">
                <h2 class="text-3xl font-bold mb-4" v-if="block.section_title">{{ block.section_title }}</h2>
                <div v-if="block.section_description" v-html="getMarkdownContent(block.section_description)" class="max-w-3xl mx-auto mb-12 text-center text-gray-500"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div v-for="(plan, index) in block.pricing_tables" :key="index" 
                     :class="[
                         'bg-white rounded-lg shadow-lg p-6 transition-transform hover:scale-105',
                         plan.is_featured ? 'ring-2 ring-blue-500' : ''
                     ]">
                    <div class="text-center">
                        <h3 class="text-xl font-bold mb-2">{{ plan.name }}</h3>
                        <div class="text-3xl font-bold mb-4">
                            {{ plan.price }}
                            <span class="text-sm text-gray-500">{{ plan.billing_period }}</span>
                        </div>
                    </div>
                    <ul class="space-y-3 mb-6">
                        <li v-for="(feature, featureIndex) in plan.features" :key="featureIndex" 
                            class="flex items-center"
                            :class="{ 'text-gray-400': !feature.is_included }">
                            <svg v-if="feature.is_included" class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg v-else class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            {{ feature.feature }}
                        </li>
                    </ul>
                    <div class="text-center">
                        <a :href="plan.button_url" 
                           :style="plan.accent_color ? { backgroundColor: plan.accent_color } : {}"
                           class="inline-block w-full px-6 py-3 text-center text-white bg-blue-600 rounded-lg hover:opacity-90 transition-colors">
                            {{ plan.button_text }}
                        </a>
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
