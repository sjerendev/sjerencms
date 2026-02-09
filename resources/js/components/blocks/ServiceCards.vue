<template>
    <section ref="sectionRef" :class="block.section_class">
        <div class="container py-16 lg:py-24 mx-auto px-6 2xl:px-0">
            <h2 v-if="block.section_title" class="mb-16 text-3xl font-bold text-center" data-card-pack-reveal>{{ block.section_title }}</h2>
            <div class="space-y-16 card-pack-grid">
                <div
                    v-for="(card, index) in block.cards"
                    :key="index"
                    class="service-card card-pack-item"
                    data-card-pack-item
                    data-card-pack-reveal
                >
                    <div class="card-content">
                        <div class="icon-container flex-shrink-0">
                            <Suspense>
                                <template #default>
                                    <Icon 
                                        :icon="getIconName(card.icon)"
                                        :style="{ color: card.icon_color || '#F87171' }"
                                        width="48"
                                        height="48"
                                    />
                                </template>
                                <template #fallback>
                                    <div class="w-12 h-12 animate-pulse bg-gray-200 rounded-full"></div>
                                </template>
                            </Suspense>
                        </div>
                        <div class="content-wrapper">
                            <h3 class="text-2xl font-bold mb-4">{{ card.title }}</h3>
                            <div v-html="getMarkdownContent(card.description)" class="prose"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { marked } from 'marked'
import DOMPurify from 'dompurify'
import { Icon } from '@iconify/vue'
import { useInViewReveal } from '@/js/composables/useInViewReveal.js'
import { useCardInteractionPack } from '@/js/composables/useCardInteractionPack.js'

const props = defineProps({
    block: {
        type: Object,
        required: true
    }
});
const sectionRef = ref(null);

// Convert icon names to Iconify format
// If it already has 'ph:' prefix, use it as is
// If it's a legacy Phosphor name, add the 'ph:' prefix
const getIconName = (iconName) => {
    if (!iconName) return '';
    if (iconName.startsWith('ph:')) return iconName;
    return `ph:${iconName}`;
};

const getMarkdownContent = (text) => {
    if (!text) return '';
    const htmlContent = marked.parse(text);
    return DOMPurify.sanitize(htmlContent);
};

const { observe } = useInViewReveal({
    itemSelector: '[data-card-pack-reveal]',
    once: true,
    stagger: 90
});

const { refresh: refreshCardInteractionPack } = useCardInteractionPack({
    sectionRef,
    itemSelector: '[data-card-pack-item]',
    maxTilt: 3.5,
    proximityRadius: 320
});

onMounted(() => {
    observe(sectionRef);
    refreshCardInteractionPack();
});
</script>

<style scoped>
.service-card {
    padding: 3rem;
    border-radius: 3rem;
    transition: transform 0.2s ease-in-out;
}

.card-content {
    display: flex;
    align-items: flex-start;
    gap: 3rem;
}

.content-wrapper {
    flex: 1;
}

.icon-container {
    background-color: var(--primary-color);
    padding: 1.5rem;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 500px) {
    .card-content {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 1.5rem;
    }
    
    .service-card {
        padding: 2rem;
    }
}

:deep(.prose) {
    max-width: none;
}

:deep(.prose p) {
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}
</style>
