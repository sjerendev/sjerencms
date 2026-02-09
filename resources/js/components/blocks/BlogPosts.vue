<template>
    <section ref="sectionRef" :class="block.section_class">
        <div class="container py-16 lg:py-24 mx-auto px-6 2xl:px-0">
            <h2
                v-if="block.section_title"
                data-reveal-item
                class="mb-8 text-3xl font-bold text-center"
            >
                {{ block.section_title }}
            </h2>
            <div :class="`grid gap-8 ${getColumnClass}`">
                <article v-for="post in posts" :key="post.id" data-reveal-item class="blog-card">
                    <router-link :to="{ name: 'post', params: { slug: post.slug } }" class="block">
                        <img v-if="block.show_hero_image && post.list_image" :src="`/storage/${post.list_image}`"
                            :alt="post.title" class="w-full h-48 object-cover rounded-t-lg">
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-3 hover:text-primary-600 transition">{{ post.title }}</h3>
                            <div v-if="post.hero_paragraph" class="text-gray-600 mb-4">{{ post.hero_paragraph }}</div>
                            <div v-if="block.show_date" class="block sm:flex items-center justify-between text-sm">
                                <div class="block sm:flex items-center text-gray-500 mb-4 sm:mb-0">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ formatDate(post.publish_date || post.created_at) }}
                                </div>
                                <div v-if="post.categories?.length" class="block sm:flex gap-2">
                                    <router-link v-for="category in post.categories" :key="category.id" :to="{
                                        name: 'page',
                                        params: { slug: 'blogg' },
                                        query: { category: category.id }
                                    }" class="category-tag text-sm font-medium mr-2 sm:mr-0">
                                        {{ category.name }}
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </router-link>
                </article>
            </div>
            <div data-reveal-item class="text-center mt-12">
                <router-link to="/blogg"
                    class="inline-block px-6 py-3 font-semibold text-[#111820] transition-colors rounded-lg bg-[#18F2B2] hover:bg-primary-600">
                    Se fler Nyheter
                </router-link>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useInViewReveal } from '@/js/composables/useInViewReveal.js';

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});

const posts = ref([]);
const sectionRef = ref(null);
const { observe } = useInViewReveal({
    itemSelector: '[data-reveal-item]',
    once: true,
    stagger: 80
});

const getColumnClass = computed(() => {
    switch (props.block.columns) {
        case '2':
            return 'grid-cols-1 lg:grid-cols-2';
        case '3':
            return 'grid-cols-1 lg:grid-cols-3';
        case '4':
            return 'grid-cols-1 lg:grid-cols-4';
        default:
            return 'grid-cols-1 lg:grid-cols-3'; // Default to 3 columns
    }
});

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('sv-SE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const applyReveal = async () => {
    await nextTick();
    observe(sectionRef);
};

onMounted(async () => {
    await applyReveal();

    try {
        const params = {
            limit: props.block.posts_count || 3
        };

        if (props.block.categories?.length) {
            params.categories = props.block.categories;
        }

        const response = await fetch('/api/posts?' + new URLSearchParams(params));
        const data = await response.json();
        posts.value = data.data;
        await applyReveal();
    } catch (error) {
        console.error('Error fetching blog posts:', error);
    }
});
</script>

<style scoped>
.blog-card {
    background-color: white;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    overflow: hidden;
    transition: transform 0.2s ease-in-out;
}

.blog-card:hover {
    transform: translateY(-2px);
}
</style>
