<template>
    <div class="container px-4 py-12 mx-auto">
        <h1 class="mb-8 text-4xl font-bold">Blog</h1>

        <div class="grid grid-cols-3 gap-8">
            <article v-for="post in posts" :key="post.id" class="post-card">
                <img
                    v-if="post.list_image || post.featured_image"
                    :src="`/storage/${post.list_image || post.featured_image}`"
                    :alt="post.title"
                    class="object-cover w-full h-48 mb-4"
                >
                <h2 class="mb-2 text-2xl font-bold">{{ post.title }}</h2>
                <p class="mb-4">{{ post.hero_paragraph }}</p>
                <router-link
                    :to="{ name: 'post', params: { slug: post.slug }}"
                    class="font-semibold text-primary-500"
                >
                    Read More
                </router-link>
            </article>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center gap-2 mt-8">
            <button
                v-for="page in totalPages"
                :key="page"
                @click="currentPage = page"
                :class="[
                    'px-4 py-2 rounded',
                    currentPage === page ? 'bg-primary-500 text-white' : 'bg-gray-200'
                ]"
            >
                {{ page }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useSeo } from '../composables/useSeo'
import { useStructuredData } from '../composables/useStructuredData'

const route = useRoute()
const posts = ref([])
const page = ref({
    title: 'Kalibr Blog',
    meta_title: 'Kalibr Blog - Webb, Design & AI Artiklar',
    meta_description: 'Läs de senaste inläggen från Kalibr om webb, design och AI. Vi delar med oss av vår kunskap och erfarenhet inom webbutveckling, design och artificiell intelligens.'
})

useSeo(page, {
    title: 'Kalibr Blog',
    description: 'Läs de senaste inläggen från Kalibr om webb, design och AI.',
    type: 'blog',
    image: '/images/blog-default.jpg'
})

// Add structured data for blog and organization
onMounted(() => {
    useStructuredData('Blog', page.value)
    useStructuredData('Organization', {})
})

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('sv-SE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const fetchPosts = async () => {
    const params = new URLSearchParams()
    if (route.query.category) {
        params.append('category', route.query.category)
    }
    const response = await fetch('/api/posts?' + params.toString())
    posts.value = (await response.json()).data
}

// Watch for query changes to refetch posts
watch(() => route.query, fetchPosts, { immediate: true })

onMounted(fetchPosts)
</script>
