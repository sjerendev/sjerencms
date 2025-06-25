<template>
	<section :class="block.section_class">
    <div class="container py-12 mx-auto">
        <div class="video-container">
            <div v-if="block.type === 'embed'" class="aspect-w-16 aspect-h-9">
                <iframe
                    :src="getEmbedUrl(block.embed_url)"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="w-full h-full"
                ></iframe>
            </div>
            <video
                v-else-if="block.video_file"
                :src="`/storage/${block.video_file}`"
                controls
                class="w-full"
            ></video>
            <div v-if="block.title || block.description" class="mt-4">
                <h3 v-if="block.title" class="mb-2 text-xl font-bold">{{ block.title }}</h3>
                <div v-if="block.description" v-html="getMarkdownContent(block.description)" class="text-gray-600"></div>
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

function getEmbedUrl(url) {
    if (url.includes('youtu.be')) {
        const id = url.split('/').pop().split('?')[0]
        return `https://www.youtube.com/embed/${id}`
    }
    return url
}

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};
</script>
