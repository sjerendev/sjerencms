<template>
    <div>
        <div v-if="page?.hero_background_type" class="relative hero-section" :style="{
            height: page.hero_height || '50svh',
            backgroundColor: page.hero_background_color,
            color: page.hero_text_color
        }">
            <img v-if="page?.hero_image" :src="`/storage/${page.hero_image}`"
                class="absolute inset-0 object-cover w-full h-full"
                :style="{ opacity: page.hero_background_type === 'both' ? 0.5 : 1 }">
            <div class="container relative z-10 px-4 py-12 mx-auto">
                <h1 class="text-4xl font-bold">{{ page.hero_headline || page.title }}</h1>
                <p v-if="page.hero_subheadline" class="mt-4 text-xl">{{ page.hero_subheadline }}</p>
                <p v-if="page.hero_paragraph" class="mt-4">{{ page.hero_paragraph }}</p>
                <a v-if="page.hero_cta_text && page.hero_cta_url" :href="page.hero_cta_url"
                    class="inline-block px-6 py-3 mt-6 text-white rounded-lg bg-primary-500">
                    {{ page.hero_cta_text }}
                </a>
            </div>
        </div>

        <!-- Content Blocks -->
        <div v-if="page?.content">
            <BlockRenderer v-for="(block, index) in page.content" :key="index" :block="block" />
        </div>

        <div v-if="page?.slug === 'blogg' && posts.length > 0" class="container px-4 py-12 mx-auto">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <router-link v-for="post in posts" :key="post.id" :to="{ name: currentLanguage === 'en' ? 'post-en' : 'post', params: { slug: post.slug } }"
                    class="overflow-hidden bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                    <img v-if="post.list_image || post.featured_image"
                        :src="`/storage/${post.list_image || post.featured_image}`" :alt="post.title"
                        class="object-cover w-full h-48">
                    <div class="p-6">
                        <h2 class="mb-2 text-2xl font-bold">{{ post.title }}</h2>
                        <div class="flex items-center justify-between mb-4 text-sm">
                            <div class="flex items-center text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ formatDate(post.publish_date || post.created_at) }}
                            </div>
                            <div v-if="post.categories?.length" class="flex gap-2">
                                <span v-for="category in post.categories" :key="category.id"
                                    class="category-tag text-sm font-medium">
                                    {{ category.name }}
                                </span>
                            </div>
                        </div>
                        <p v-if="post.hero_paragraph" class="mb-4 text-gray-600">{{ post.hero_paragraph }}</p>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import BlockRenderer from '../components/BlockRenderer.vue'
import { useSeo } from '../composables/useSeo'
import { useLanguage } from '../composables/useLanguage'

const route = useRoute()
const { currentLanguage } = useLanguage()
const page = ref(null)
const posts = ref([])

const formatDate = (date) => {
    if (!date) return '';
    const locale = currentLanguage.value === 'en' ? 'en-US' : 'sv-SE';
    return new Date(date).toLocaleDateString(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const loadPage = async () => {
    try {
        const response = await fetch(`/api/pages/${route.params.slug}?lang=${currentLanguage.value}`)
        page.value = await response.json()

        // Check for blog page in both languages
        const isBlogPage = route.params.slug === 'blogg' || route.params.slug === 'blog'
        if (isBlogPage) {
            const params = new URLSearchParams()
            params.append('lang', currentLanguage.value)
            if (route.query.category) {
                params.append('categories', route.query.category)
            }
            const response = await fetch('/api/posts?' + params.toString())
            const data = await response.json()
            posts.value = data.data
        } else {
            posts.value = []
        }
    } catch (error) {
        console.error('Error loading page:', error)
    }
}

useSeo(page, {
    title: 'Kalibr',
    description: 'Kalibr. är en Webb, Design & AI-byrå med bas i Malmö men har kunder i hela Sverige.',
    type: 'article'
})

// Watch for route changes
watch(
    () => route.params.slug,
    () => loadPage()
)

onMounted(() => {
    loadPage()
})
</script>
