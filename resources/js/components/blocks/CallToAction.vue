<template>
    <section
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
            <h2 class="mb-4 text-3xl font-bold">{{ block.headline }}</h2>
            <div v-html="getMarkdownContent(block.text)" class="mb-6"></div>
            <a
                :href="block.button_url"
                class="inline-block px-8 py-3 font-semibold rounded-lg"
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
import { computed } from 'vue'
import { marked } from 'marked';
import DOMPurify from 'dompurify';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
})

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
</script>
