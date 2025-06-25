<template>
    <div class="blogg">
        <div
            v-if="post?.hero_background_type"
            class="relative flex items-center hero-section"
            :style="{
                height: post.hero_height || '50svh',
                backgroundColor: post.hero_background_color,
                color: post.hero_text_color
            }"
        >
            <img
                v-if="post?.hero_image"
                :src="`/storage/${post.hero_image}`"
                class="absolute inset-0 object-cover w-full h-full"
                :style="{ opacity: post.hero_background_type === 'both' ? 0.5 : 1 }"
            >
            <div class="container relative z-10 px-4 py-12 mx-auto">
                <h1 class="text-4xl font-bold">{{ post?.hero_headline || post?.title }}</h1>
                <div v-if="post" class="flex items-center justify-between mt-4 text-sm">
                    <div class="flex items-center text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ formatDate(post.publish_date || post.created_at) }}
                    </div>
                    <div v-if="post.categories?.length" class="flex gap-2">
                        <router-link 
                            v-for="category in post.categories" 
                            :key="category.id"
                            :to="{ 
                                name: 'page',
                                params: { slug: 'blogg' },
                                query: { category: category.id }
                            }"
                            class="text-sm font-medium text-primary-600 hover:text-primary-700">
                            {{ category.name }} 
                        </router-link>
                    </div>
                </div>
                <p v-if="post.hero_subheadline" class="mt-4 text-xl">{{ post.hero_subheadline }}</p>
                <p v-if="post.hero_paragraph" class="mt-4">{{ post.hero_paragraph }}</p>
                <a
                    v-if="post.hero_cta_text && post.hero_cta_url"
                    :href="post.hero_cta_url"
                    class="inline-block px-6 py-3 mt-6 text-white rounded-lg bg-primary-500"
                >
                    {{ post.hero_cta_text }}
                </a>
            </div>
        </div>
        <div v-else-if="post" class="blog-intro">
            <h1 class="text-4xl font-bold">{{ post.title }}</h1>
            <div class="flex items-center justify-between mt-4 text-sm">
                <div class="flex items-center text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ formatDate(post.publish_date || post.created_at) }}
                </div>
                <div v-if="post.categories?.length" class="flex gap-2">
                    <router-link 
                        v-for="category in post.categories" 
                        :key="category.id"
                        :to="{ 
                            name: 'page',
                            params: { slug: 'blogg' },
                            query: { category: category.id }
                        }"
                        class="text-sm font-medium text-primary-600 hover:text-primary-700">
                        {{ category.name }}
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div v-if="post?.content" class="container mx-auto px-4 py-12">
            <div class="prose prose-lg max-w-none" v-html="renderedContent"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import BlockRenderer from '../components/BlockRenderer.vue'
import { useSeo } from '../composables/useSeo'
import { useStructuredData } from '../composables/useStructuredData'
import { marked } from 'marked'

const route = useRoute()
const post = ref(null)

const renderedContent = computed(() => {
    if (!post.value?.content) return ''
    return marked(post.value.content)
})

useSeo(post, {
    title: 'Kalibr Blog',
    description: 'Läs de senaste inläggen från Kalibr om webb, design och AI.',
    type: 'article',
    image: '/images/blog-default.jpg'
})

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('sv-SE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

onMounted(async () => {
    const response = await fetch(`/api/posts/${route.params.slug}`)
    post.value = await response.json()
    
    // Add structured data
    useStructuredData('BlogPosting', post.value)
    
    // Add article:published_time meta tag
    if (post.value?.published_at) {
        const head = document.head
        const meta = document.createElement('meta')
        meta.setAttribute('property', 'article:published_time')
        meta.setAttribute('content', new Date(post.value.published_at).toISOString())
        head.appendChild(meta)
    }
})
</script>
